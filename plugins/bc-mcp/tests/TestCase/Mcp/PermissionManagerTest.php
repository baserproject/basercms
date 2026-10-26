<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\PermissionManager;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionManagerTest
 */
class PermissionManagerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        // Api/Admin を限定アクセスで許可し、アクセスルールを持たないグループ
        UserGroupFactory::make([
            'id' => 2,
            'name' => 'operators',
            'title' => 'operators',
            'auth_prefix' => 'Admin,Api/Admin',
            'auth_prefix_settings' => json_encode(['Admin' => ['type' => '2'], 'Api/Admin' => ['type' => '2']]),
        ])->persist();
    }

    /**
     * test getPermissionUrl
     *
     * 管理 API のパスは ADMIN_PREFIX に依存しないため、判定 URL も管理画面のプレフィックスに依存しない
     */
    public function testGetPermissionUrl()
    {
        $permissionManager = new PermissionManager();
        $this->assertEquals(
            ['GET' => '/baser/api/admin/bc-blog/blog_posts/index.json'],
            $permissionManager->getPermissionUrl('getBlogPosts', [])
        );

        Configure::write('BcApp.adminPrefix', 'admin_test');
        $this->assertEquals(
            ['GET' => '/baser/api/admin/bc-blog/blog_posts/index.json'],
            $permissionManager->getPermissionUrl('getBlogPosts', [])
        );
    }

    /**
     * test checkPermission
     *
     * 管理画面のプレフィックスを変更しても、アクセスルールのない操作は拒否する
     */
    public function testCheckPermissionWithChangedAdminPrefix()
    {
        $permissionManager = new PermissionManager();
        $this->assertFalse($permissionManager->checkPermission('deleteCustomTable', [2], ['id' => 1]));

        Configure::write('BcApp.adminPrefix', 'admin_test');
        $this->assertFalse($permissionManager->checkPermission('deleteCustomTable', [2], ['id' => 1]));
    }

    /**
     * test checkPermission
     *
     * 管理画面のプレフィックスを変更しても、アクセスルールで許可した操作は許可する
     */
    public function testCheckPermissionAllowedWithChangedAdminPrefix()
    {
        PermissionFactory::make([
            'user_group_id' => 2,
            'url' => '/baser/api/admin/bc-blog/blog_posts/index.json',
            'method' => 'GET',
            'auth' => true,
            'status' => true,
        ])->persist();
        $permissionManager = new PermissionManager();
        $this->assertTrue($permissionManager->checkPermission('getBlogPosts', [2], []));

        Configure::write('BcApp.adminPrefix', 'admin_test');
        $this->assertTrue($permissionManager->checkPermission('getBlogPosts', [2], []));
    }

    /**
     * test isApiAdminUrl
     */
    public function testIsApiAdminUrl()
    {
        $permissionManager = new PermissionManager();
        // 管理 API
        $this->assertTrue($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/baser/api/admin/bc-blog/blog_posts/index.json']));
        $this->assertTrue($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/baser/api/admin/bc-blog/blog_posts/delete/1.json']));
        // 管理 API ではないプレフィックス
        $this->assertFalse($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/baser/admin/bc-blog/blog_posts/index']));
        $this->assertFalse($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/baser/api/bc-blog/blog_posts/index.json']));
        // 管理画面のプレフィックスで組み立てた、実在しない管理 API の URL
        $this->assertFalse($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/baser/api/admin_test/bc-blog/blog_posts/index.json']));
        // フロント・解決できない URL
        $this->assertFalse($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/']));
        $this->assertFalse($this->execPrivateMethod($permissionManager, 'isApiAdminUrl', ['/not-exists/foo/bar']));
    }

    /**
     * test checkPermission
     *
     * 判定 URL が管理 API として解決できない場合は拒否する
     */
    public function testCheckPermissionWithUnresolvableUrl()
    {
        Configure::write('BcPrefixAuth.Api/Admin.alias', '/not-exists');
        $permissionManager = new PermissionManager();
        $this->assertFalse($permissionManager->checkPermission('deleteCustomTable', [2], ['id' => 1]));
    }

}
