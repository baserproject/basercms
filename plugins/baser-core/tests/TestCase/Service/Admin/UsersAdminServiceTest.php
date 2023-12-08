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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\UsersAdminService;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class UsersAdminServiceTest
 * @property UsersAdminService $Users
 */
class UsersAdminServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * @var UsersAdminService|null
     */
    public $Users = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->Users = new UsersAdminService();
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
     * test construct
     * @return void
     */
    public function testConstruct(){
        $this->assertTrue(isset($this->Users->UserGroupsService));
    }

    /**
     * Test isEditable
     * @param int $loginId
     * @param int $postId
     * @param bool $expected
     * @dataProvider isEditableDataProvider
     */
    public function testIsEditable($loginId, $postId, $expected)
    {
        $request = $this->getRequest('/baser/admin');
        if ($loginId) {
            $this->loginAdmin($request, $loginId);
        }
        $result = $this->Users->isEditable($postId);
        $this->assertEquals($expected, $result);
    }

    public function isEditableDataProvider()
    {
        return [
            [null, null, false],  // 未ログイン新規
            [1, null, false], //ログイン新規
            [null, 1, false],   // 未ログイン更新
            [1, 1, true],   // ログイン更新
            [2, 1, false]   // 管理者以外ログイン更新
        ];
    }

    /**
     * Test isDeletable
     * @param int $loginId
     * @param int $postId
     * @param bool $expected
     * @dataProvider isDeletableDataProvider
     */
    public function testIsDeletable($loginId, $postId, $expected)
    {
        $request = $this->getRequest('/baser/admin');
        if ($loginId) {
            $this->loginAdmin($request, $loginId);
        }
        $result = $this->Users->isDeletable($postId);
        $this->assertEquals($expected, $result);
    }

    public function isDeletableDataProvider()
    {
        return [
            [null, null, false],  // 未ログインデータ不完全
            [null, 1, false],   // 未ログイン削除
            [1, 2, true],   // 管理者ログイン削除
            [1, 1, false],   // 管理者ログイン自分を削除
            [2, 1, true]   // 非管理者ログイン削除
        ];
    }

    /**
     * Test isSelf
     * @param int $loginId
     * @param int $postId
     * @param bool $expected
     * @dataProvider isSelfUpdateDataProvider
     */
    public function testIsSelf($loginId, $postId, $expected)
    {
        $request = $this->getRequest();
        if ($loginId) {
            $request = $this->loginAdmin($request, $loginId);
        }
        Router::setRequest($request);
        $result = $this->Users->isSelf($postId);
        $this->assertEquals($expected, $result);
    }

    public function isSelfUpdateDataProvider()
    {
        return [
            [1, 1, true],        // 自身を更新
            [1, 2, false],       // 他人を更新
            [null, null, false], // 新規登録
            [null, 1, false]     // 更新
        ];
    }

    /**
     * test isUserGroupEditable
     */
    public function testIsUserGroupEditable()
    {
        // 新規ユーザー更新
        $this->assertTrue($this->Users->isUserGroupEditable(null));
        // システム管理ユーザーでの他ユーザー更新
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertTrue($this->Users->isUserGroupEditable(2));
        // サイト運営ユーザーで他ユーザー更新
        $this->loginAdmin($this->getRequest('/baser/admin'), 2);
        $this->assertTrue($this->Users->isUserGroupEditable(1));
        // サイト運営ユーザーで自身を更新
        $this->assertFalse($this->Users->isUserGroupEditable(2));
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $this->loginAdmin($this->getRequest());
        $this->assertTrue(count($this->Users->getViewVarsForEdit($this->Users->get(1))) >= 3);

        $this->assertEquals([
            1 => 'システム管理',
            2 => 'サイト運営者',
            3 => 'その他のグループ',
        ], $this->Users->getViewVarsForEdit($this->Users->get(1))['userGroupList']);
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        $this->loginAdmin($this->getRequest());
        $this->assertTrue(count($this->Users->getViewVarsForAdd($this->Users->getNew())) >= 2);

        $this->assertEquals([
            1 => 'システム管理',
            2 => 'サイト運営者',
            3 => 'その他のグループ',
        ], $this->Users->getViewVarsForEdit($this->Users->get(1))['userGroupList']);
    }

    /**
     * test getViewVarsForLogin
     */
    public function test_getViewVarsForLogin()
    {
        $result = $this->Users->getViewVarsForLogin($this->getRequest());
        $this->assertArrayHasKey('savedEnable', $result);
        $this->assertArrayHasKey('isEnableLoginCredit', $result);
    }

}
