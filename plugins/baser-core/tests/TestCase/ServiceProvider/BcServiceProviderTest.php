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
        $userService = $container->get('BaserCore\Service\UsersServiceInterface');
        $userGroupService = $container->get('BaserCore\Service\UserGroupsServiceInterface');
        $pluginService = $container->get('BaserCore\Service\PluginsServiceInterface');
        $permissionService = $container->get('BaserCore\Service\PermissionsServiceInterface');
        $DblogsService = $container->get('BaserCore\Service\DblogsServiceInterface');
        $siteService = $container->get('BaserCore\Service\SitesServiceInterface');
        $ContentsService = $container->get('BaserCore\Service\ContentsServiceInterface');
        $contentFolderService = $container->get('BaserCore\Service\ContentFoldersServiceInterface');
        $pageService = $container->get('BaserCore\Service\PagesServiceInterface');
        $searchIndexService = $container->get('BaserCore\Service\SearchIndexesServiceInterface');
        $this->assertEquals('BaserCore\Service\UsersService', get_class($userService));
        $this->assertEquals('BaserCore\Service\UserGroupsService', get_class($userGroupService));
        $this->assertEquals('BaserCore\Service\PluginsService', get_class($pluginService));
        $this->assertEquals('BaserCore\Service\PermissionsService', get_class($permissionService));
        $this->assertEquals('BaserCore\Service\DblogsService', get_class($DblogsService));
        $this->assertEquals('BaserCore\Service\SitesService', get_class($siteService));
        $this->assertEquals('BaserCore\Service\ContentsService', get_class($ContentsService));
        $this->assertEquals('BaserCore\Service\ContentFoldersService', get_class($contentFolderService));
        $this->assertEquals('BaserCore\Service\PagesService', get_class($pageService));
        $this->assertEquals('BaserCore\Service\SearchIndexesService', get_class($searchIndexService));
    }

}
