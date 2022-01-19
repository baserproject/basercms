<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\ServiceProvider;

use BaserCore\ServiceProvider\BcServiceProvider;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Container;

/**
 * Class BcServiceProviderTest
 * @package BaserCore\Test\TestCase\ServiceProvider
 * @property BcServiceProvider $Provider
 */
class BcServiceProviderTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Provider = new BcServiceProvider();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Provider);
        parent::tearDown();
    }

    /**
     * Test services
     */
    public function testServices()
    {
        $container = new Container();
        $this->Provider->services($container);
        $userService = $container->get('BaserCore\Service\UserServiceInterface');
        $userGroupService = $container->get('BaserCore\Service\UserGroupServiceInterface');
        $pluginService = $container->get('BaserCore\Service\PluginServiceInterface');
        $permissionService = $container->get('BaserCore\Service\PermissionServiceInterface');
        $DblogService = $container->get('BaserCore\Service\DblogServiceInterface');
        $siteService = $container->get('BaserCore\Service\SiteServiceInterface');
        $ContentService = $container->get('BaserCore\Service\ContentServiceInterface');
        $contentFolderService = $container->get('BaserCore\Service\ContentFolderServiceInterface');
        $pageService = $container->get('BaserCore\Service\PageServiceInterface');
        $this->assertEquals('BaserCore\Service\UserService', get_class($userService));
        $this->assertEquals('BaserCore\Service\UserGroupService', get_class($userGroupService));
        $this->assertEquals('BaserCore\Service\PluginService', get_class($pluginService));
        $this->assertEquals('BaserCore\Service\PermissionService', get_class($permissionService));
        $this->assertEquals('BaserCore\Service\DblogService', get_class($DblogService));
        $this->assertEquals('BaserCore\Service\SiteService', get_class($siteService));
        $this->assertEquals('BaserCore\Service\ContentService', get_class($ContentService));
        $this->assertEquals('BaserCore\Service\ContentFolderService', get_class($contentFolderService));
        $this->assertEquals('BaserCore\Service\PageService', get_class($pageService));
    }

}
