<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use Cake\Core\Configure;
use Cake\Validation\Validator;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Model\Table\UsersTable Test Case
 *
 * @property UsersTable $Users
 */
class UsersTableTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var UsersTable
     */
    public $Users;

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Users = $this->getTableLocator()->get('BaserCore.Users');
        $this->LoginStores = $this->getTableLocator()->get('BaserCore.LoginStores');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals('users', $this->Users->getTable());
        $this->assertEquals('name', $this->Users->getDisplayField());
        $this->assertEquals('id', $this->Users->getPrimaryKey());
        $this->assertTrue($this->Users->hasBehavior('Timestamp'));
        $this->assertEquals('UserGroups', $this->Users->getAssociation('UserGroups')->getName());
    }

    /**
     * Test beforeMarshal
     */
    public function testBeforeMarshal()
    {
        $user = $this->Users->newEntity(
            ['password_1' => 'testtest'],
            ['validate' => false]
        );
        $this->assertNotEmpty($user->password);
    }

    /**
     * Test afterMarshal
     */
    public function testAfterMarshal()
    {
        $user = $this->Users->newEntity([
            'password' => ''
        ]);
        $this->assertEquals($user->getError('password_1'), []);
        $this->assertEquals($user->getError('password_2'), []);
    }

    /**
     * Test afterSave
     */
    public function testAfterSave()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        // ユーザ更新時、自動ログインのデータを削除する
        $user = $this->Users->find('all')->first();
        $this->LoginStores->addKey('Admin', $user->id);
        $dataCount = $this->LoginStores->find('all')
            ->where(['user_id' => $user->id])
            ->count();
        $this->assertNotSame($dataCount, 0);

        $user->real_name_1 = $user->real_name_1 . 'modify';
        $this->Users->save($user);

        $dataCount = $this->LoginStores->find('all')
            ->where(['user_id' => $user->id])
            ->count();
        $this->assertSame($dataCount, 0);
    }

    /**
     * Test validationDefault
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = $this->Users->validationDefault(new Validator());
        $fields = [];
        foreach($validator->getIterator() as $key => $value) {
            $fields[] = $key;
        }
        $this->assertEquals(['id', 'name', 'real_name_1', 'real_name_2', 'nickname', 'user_groups', 'email', 'password'], $fields);
    }

    /**
     * Test validationPassword
     * @param $isValid 妥当でない場合、$validator->validateからエラーが返る
     * @param $data パスワード文字列
     * @param $allowSimplePassword 簡易なパスワードを許可
     * @param $passwordRule パスワードの設定ルール
     * @return void
     * @dataProvider validationPasswordDataProvider
     */
    public function testValidationPassword($isValid, $data, $allowSimplePassword, $passwordRule = [])
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        if ($allowSimplePassword) {
            $siteConfigsService->setValue('allow_simple_password', 1);
        } else {
            $siteConfigsService->setValue('allow_simple_password', 0);
        }
        if ($passwordRule) {
            Configure::write('BcApp.passwordRule', $passwordRule);
        }

        $validator = $this->Users->validationPassword(new Validator());
        $validator->setProvider('table', $this->Users);
        if ($isValid) {
            $this->assertEmpty($validator->validate($data));
        } else {
            $this->assertNotEmpty($validator->validate($data));
        }
    }

    public static function validationPasswordDataProvider()
    {
        $exceedMax = str_repeat('a', 256);

        return [
            // 簡易なパスワードを許可

            // - OK
            [true, ['password' => 'testtest', 'password_1' => 'testtest', 'password_2' => 'testtest'], true],
            // - 文字数が少ない
            [false, ['password' => 'test', 'password_1' => 'test', 'password_2' => 'test'], true],
            // - 文字数が多い
            [false, ['password' => $exceedMax, 'password_1' => $exceedMax, 'password_2' => $exceedMax], true],
            // - 不適切な文字が入っている
            [false, ['password' => '^^^^^^^^', 'password_1' => '^^^^^^^^', 'password_2' => '^^^^^^^^'], true],
            // - パスワードが異なる
            [false, ['password' => 'testtest', 'password_1' => 'test', 'password_2' => 'testtest'], true],

            // 簡易なパスワードを許可しない

            // - OK
            [true, ['password' => 'TestPassword1!', 'password_1' => 'TestPassword1!', 'password_2' => 'TestPassword1!'], false, [
                'minLength' => 12,
                'requiredCharacterTypes' => [ 'numeric', 'uppercase', 'lowercase', 'symbol' ],
            ]],
            [true, ['password' => '1234', 'password_1' => '1234', 'password_2' => '1234'], false, [
                'minLength' => 4,
                'requiredCharacterTypes' => [ 'numeric' ],
            ]],
            [true, ['password' => 'AAAA', 'password_1' => 'AAAA', 'password_2' => 'AAAA'], false, [
                'minLength' => 4,
                'requiredCharacterTypes' => [ 'uppercase' ],
            ]],
            [true, ['password' => 'aaaa', 'password_1' => 'aaaa', 'password_2' => 'aaaa'], false, [
                'minLength' => 4,
                'requiredCharacterTypes' => [ 'lowercase' ],
            ]],
            [true, ['password' => '!!!!', 'password_1' => '!!!!', 'password_2' => '!!!!'], false, [
                'minLength' => 4,
                'requiredCharacterTypes' => [ 'symbol' ],
            ]],
            // - 文字数が少ない
            [false, ['password' => 'TestPassword1!', 'password_1' => 'TestPassword1!', 'password_2' => 'TestPassword1!'], false, [
                'minLength' => 24,
                'requiredCharacterTypes' => [ 'numeric', 'uppercase', 'lowercase', 'symbol' ],
            ]],
            // - 文字種が少ない
            [false, ['password' => '1234', 'password_1' => '1234', 'password_2' => '1234'], false, [
                'minLength' => 4,
                'requiredCharacterTypes' => [ 'numeric', 'uppercase', 'lowercase', 'symbol' ],
            ]],
        ];
    }

    /**
     * Test validationPasswordUpdate
     * @return void
     */
    public function testValidationPasswordUpdate()
    {
        $validator = $this->Users->validationPasswordUpdate(new Validator());

        $this->assertEmpty($validator->validate([
            'password' => 'TestPassword1!', 'password_1' => 'TestPassword1!', 'password_2' => 'TestPassword1!',
        ]));

        $this->assertNotEmpty($validator->validate([]));
    }

    /**
     * Test validationNew
     *
     * @return void
     */
    public function testValidationNew()
    {
        $user = $this->Users->newEntity([
            'password' => '',
            'password_1' => '',
            'password_2' => ''
        ], ['validate' => 'new']);
        $this->assertEquals($user->getError('password')['_empty'], __d('baser_core', 'パスワードを入力してください。'));
    }

    /**
     * Test getControlSource
     */
    public function testGetControlSource()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $list = $this->Users->getControlSource('user_group_id')->toArray();
        $this->assertEquals('システム管理', $list[1]);
    }

    /**
     * testGetUserList
     *
     * @return void
     */
    public function testGetUserList(): void
    {
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loadFixtureScenario(LoginStoresScenario::class);
        $result = $this->Users->getUserList(['name' => 'baser admin']);
        $this->assertCount(1, $result);
        $this->assertEquals('ニックネーム1', $result[1]);
    }

    /**
     * test findAvailable
     */
    public function test_findAvailable()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->getRequest('/baser/admin');
        $entity = $this->Users->findAvailable($this->Users->find())->first();
        $this->assertTrue(isset($entity->user_groups));
        $this->assertTrue($entity->status);
        $this->assertNull($this->Users->findAvailable($this->Users->find()->where(['Users.id' => 3]))->first());
    }

}
