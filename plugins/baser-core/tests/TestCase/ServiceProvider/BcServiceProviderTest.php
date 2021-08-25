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
        $usersService = $container->get('BaserCore\Service\UsersServiceInterface');
        $userGroupsService = $container->get('BaserCore\Service\UserGroupsServiceInterface');
        $pluginsService = $container->get('BaserCore\Service\PluginsServiceInterface');
        $permissionService = $container->get('BaserCore\Service\PermissionServiceInterface');
        $DblogService = $container->get('BaserCore\Service\DblogServiceInterface');
        $sitesService = $container->get('BaserCore\Service\SitesServiceInterface');
        $ContentService = $container->get('BaserCore\Service\ContentServiceInterface');
        $contentFoldersService = $container->get('BaserCore\Service\ContentFoldersServiceInterface');
        $this->assertEquals('BaserCore\Service\UsersService', get_class($usersService));
        $this->assertEquals('BaserCore\Service\UserGroupsService', get_class($userGroupsService));
        $this->assertEquals('BaserCore\Service\PluginsService', get_class($pluginsService));
        $this->assertEquals('BaserCore\Service\PermissionService', get_class($permissionService));
        $this->assertEquals('BaserCore\Service\DblogService', get_class($DblogService));
        $this->assertEquals('BaserCore\Service\SitesService', get_class($sitesService));
        $this->assertEquals('BaserCore\Service\ContentService', get_class($ContentService));
        $this->assertEquals('BaserCore\Service\ContentFoldersService', get_class($contentFoldersService));
        $this->assertEquals('BaserCore\Service\ContentFoldersService', get_class($contentFoldersService));
    }

}
