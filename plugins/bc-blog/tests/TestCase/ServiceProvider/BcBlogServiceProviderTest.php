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

namespace BcBlog\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\ServiceProvider\BcBlogServiceProvider;
use Cake\Core\Container;

/**
 * Class BcServiceProviderTest
 * @package BaserCore\Test\TestCase\ServiceProvider
 * @property BcBlogServiceProvider $Provider
 */
class BcBlogServiceProviderTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Provider = new BcBlogServiceProvider();
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
        $blogCategoriesService = $container->get('BcBlog\Service\BlogCategoriesServiceInterface');
        $this->assertEquals('BcBlog\Service\BlogCategoriesService', get_class($blogCategoriesService));
        $blogCategoriesAdminService = $container->get('BcBlog\Service\Admin\BlogCategoriesAdminServiceInterface');
        $this->assertEquals('BcBlog\Service\Admin\BlogCategoriesAdminService', get_class($blogCategoriesAdminService));
        $blogContentsService = $container->get('BcBlog\Service\BlogContentsServiceInterface');
        $this->assertEquals('BcBlog\Service\BlogContentsService', get_class($blogContentsService));
        $blogContentsAdminService = $container->get('BcBlog\Service\Admin\BlogContentsAdminServiceInterface');
        $this->assertEquals('BcBlog\Service\Admin\BlogContentsAdminService', get_class($blogContentsAdminService));
    }

}
