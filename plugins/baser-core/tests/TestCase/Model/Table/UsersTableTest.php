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
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
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
     * Test validationPasswordUpdate
     * @param $isValid 妥当でない場合、$validator->validateからエラーが返る
     * @param $data パスワード文字列
     * @return void
     * @dataProvider validationPasswordUpdateDataProvider
     */
    public function testValidationPasswordUpdate($isValid, $data)
    {
        $validator = $this->Users->validationPasswordUpdate(new Validator());
        $validator->setProvider('table', $this->Users);
        if ($isValid) {
            $this->assertEmpty($validator->validate($data));
        } else {
            $this->assertNotEmpty($validator->validate($data));
        }
    }

    public static function validationPasswordUpdateDataProvider()
    {
        $exceedMax = "testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest";
        return [
            // 妥当な例
            [true, ['password' => 'testtest', 'password_1' => 'testtest', 'password_2' => 'testtest']],
            // 文字数が少ない場合
            [false, ['password' => 'test', 'password_1' => 'test', 'password_2' => 'test']],
            // 文字数が少ない場合
            [false, ['password' => $exceedMax, 'password_1' => $exceedMax, 'password_2' => $exceedMax]],
            // 不適切な文字が入ってる場合
            [false, ['password' => '^^^^^^^^', 'password_1' => '^^^^^^^^', 'password_2' => '^^^^^^^^']],
            // パスワードが異なる例
            [false, ['password' => 'testtest', 'password_1' => 'test', 'password_2' => 'testtest']],
        ];
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
