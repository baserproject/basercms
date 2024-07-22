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

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\User;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class UserTest
 */
class UserTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * @var User
     */
    public $User;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->User = $this->getTableLocator()->get('BaserCore.Users')->get(1, contain: 'UserGroups');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->User);
        parent::tearDown();
    }

    /**
     * Test _setPassword
     */
    public function testSetPassword()
    {
        $beforePasswordModified = $this->User->password_modified;
        $this->User->set('password', 'testtest123A!');
        $this->assertNotEquals('testtest', $this->User->password);
        $this->assertGreaterThan($beforePasswordModified, $this->User->password_modified);
    }

    /**
     * test isEditableUser
     */
    public function testIsEditableUser()
    {
        //$isSuper = true, return true
        $this->assertTrue($this->User->isEditableUser(UserFactory::make(['id' => 2])->getEntity()));

        //$this->id === $targetUser->id、return true
        $this->assertTrue($this->User->isEditableUser(UserFactory::make(['id' => 1])->getEntity()));

        //isAdminではない場合、return true
        $this->assertTrue($this->User->isEditableUser(UserFactory::make(['id' => 3])->getEntity()));

        //他のAdminアカウトを編集する場合、return false
        Configure::write('BcApp.superUserId', 2);
        UserFactory::make(['id' => 4])->persist();
        UsersUserGroupFactory::make(['user_id' => 4, 'user_group_id' => 1])->persist();
        $user = $this->getTableLocator()->get('BaserCore.Users')->get(4, contain: 'UserGroups');

        $this->assertFalse($this->User->isEditableUser($user));
    }

    /**
     * Test isAdmin
     */
    public function testIsAdmin()
    {
        $this->assertTrue($this->User->isAdmin());
    }

    /**
     * test isDeletableUser
     */
    public function testIsDeletableUser()
    {
        //データ生成
        //スーパーユーザー
        UserFactory::make(['id' => 2])->persist();
        Configure::write('BcApp.superUserId', 2);
        //Adminーザー
        UserFactory::make(['id' => 3])->persist();
        UsersUserGroupFactory::make(['user_id' => 3, 'user_group_id' => 1])->persist();
        //スーパーでもAdminでもないーザー
        UserFactory::make(['id' => 4])->persist();

        //自身がAdminを設定
        $this->User = $this->getTableLocator()->get('BaserCore.Users')->get(1, contain: 'UserGroups');

        //ターゲットがAdmin：false
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(3, contain: 'UserGroups')));
        //ターゲットがスーパーじゃない：true
        $this->assertTrue($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(4, contain: 'UserGroups')));
        //ターゲットがスーパー：false
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(2, contain: 'UserGroups')));

        //自身がスーパーを設定
        $this->User = $this->getTableLocator()->get('BaserCore.Users')->get(2, contain: 'UserGroups');
        //ターゲットがスーパー：false
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(2, contain: 'UserGroups')));
        //ターゲットがスーパーじゃない：true
        $this->assertTrue($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(1, contain: 'UserGroups')));

        //自身がスーパーでもAdminでもないを設定 ：false
        $this->User = $this->getTableLocator()->get('BaserCore.Users')->get(4, contain: 'UserGroups');
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(1, contain: 'UserGroups')));
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(2, contain: 'UserGroups')));
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(3, contain: 'UserGroups')));
        $this->assertFalse($this->User->isDeletableUser($this->getTableLocator()->get('BaserCore.Users')->get(4, contain: 'UserGroups')));
    }

    /**
     * ユーザー名を整形して表示する
     * @param string $nickname
     * @param string $realName1
     * @param string $realName2
     * @param string $expect
     * @return void
     * @dataProvider getUserNameDataProvider
     */
    public function testGetDisplayName($nickname, $realName1, $realName2, $expect)
    {
        $userTable = $this->getTableLocator()->get('BaserCore.Users');
        /** @var User $user */
        $user = $userTable->newEntity([
            'nickname' => $nickname,
            'real_name_1' => $realName1,
            'real_name_2' => $realName2,
        ]);
        $result = $user->getDisplayName();
        $this->assertEquals($expect, $result);
    }

    public static function getUserNameDataProvider()
    {
        return [
            ['aiueo', 'yamada', 'tarou', 'aiueo'],
            ['', 'yamada', 'tarou', 'yamada tarou'],
            ['', 'yamada', '', 'yamada'],
            ['', '', '', 'undefined'],
        ];
    }

    /**
     * test getAuthPrefixes
     */
    public function test_getAuthPrefixes()
    {
        //user group is empty
        $user = new User();
        $user_groups = $user->getAuthPrefixes();
        $this->assertEquals([], $user_groups);

        //user group is not empty
        $user_groups = $this->User->getAuthPrefixes();
        $this->assertEquals([0 => 'Admin', 1 => 'Api/Admin'], $user_groups);
    }

    public function test_isSuper()
    {
        //user is a superuser
        Configure::write('BcApp.superUserId', 1);
        $user = new User();
        $user->id = 1;
        $this->assertTrue($user->isSuper());

        //user is not a superuser
        $user->id = 2;
        $this->assertFalse($user->isSuper());
    }

    public function testIsEnableLoginAgent()
    {
        //status = false場合、return true
        $targetUser = UserFactory::make(['status' => false])->getEntity();
        $this->assertFalse($this->User->isEnableLoginAgent($targetUser));

        //status = true && isSuper = false && isAdmin = false場合、return true
        Configure::write('BcApp.superUserId', 1);
        $targetUser = UserFactory::make(['id' => 2])->getEntity();
        $this->assertTrue($this->User->isEnableLoginAgent($targetUser));

        //status = true && isSuper = false && isAdmin = true場合、return false
        $this->assertFalse($this->User->isEnableLoginAgent($this->User));
    }
}
