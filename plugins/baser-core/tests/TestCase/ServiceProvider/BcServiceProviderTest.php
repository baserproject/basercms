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
        $permissionsService = $container->get('BaserCore\Service\PermissionsServiceInterface');
        $DblogsService = $container->get('BaserCore\Service\DblogsServiceInterface');
        $sitesService = $container->get('BaserCore\Service\SitesServiceInterface');
        $contentsService = $container->get('BaserCore\Service\ContentsServiceInterface');
        $this->assertEquals('BaserCore\Service\UsersService', get_class($usersService));
        $this->assertEquals('BaserCore\Service\UserGroupsService', get_class($userGroupsService));
        $this->assertEquals('BaserCore\Service\PluginsService', get_class($pluginsService));
        $this->assertEquals('BaserCore\Service\PermissionsService', get_class($permissionsService));
        $this->assertEquals('BaserCore\Service\DblogsService', get_class($DblogsService));
        $this->assertEquals('BaserCore\Service\SitesService', get_class($sitesService));
        $this->assertEquals('BaserCore\Service\ContentsService', get_class($contentsService));
    }

}
