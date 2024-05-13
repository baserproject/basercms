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

namespace BcCustomContent\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Service\Admin\CustomContentsAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomEntriesAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomFieldsAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomLinksAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomTablesAdminServiceInterface;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Service\Front\CustomContentFrontServiceInterface;
use BcCustomContent\ServiceProvider\BcCustomContentServiceProvider;
use Cake\Core\Container;

/**
 * Class BcCustomContentServiceProviderTest
 */
class BcCustomContentServiceProviderTest extends BcTestCase
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcCustomContentServiceProvider = new BcCustomContentServiceProvider();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BcCustomContentServiceProvider);
        parent::tearDown();
    }

    /**
     * test services
     */
    public function test_services()
    {
        $container = new Container();
        $this->BcCustomContentServiceProvider->services($container);
        $this->assertTrue($container->has(CustomContentsServiceInterface::class));
        $this->assertTrue($container->has(CustomContentsAdminServiceInterface::class));
        $this->assertTrue($container->has(CustomTablesServiceInterface::class));
        $this->assertTrue($container->has(CustomTablesAdminServiceInterface::class));
        $this->assertTrue($container->has(CustomFieldsServiceInterface::class));
        $this->assertTrue($container->has(CustomFieldsAdminServiceInterface::class));
        $this->assertTrue($container->has(CustomEntriesServiceInterface::class));
        $this->assertTrue($container->has(CustomEntriesAdminServiceInterface::class));
        $this->assertTrue($container->has(CustomContentFrontServiceInterface::class));
        $this->assertTrue($container->has(CustomLinksServiceInterface::class));
        $this->assertTrue($container->has(CustomLinksAdminServiceInterface::class));
    }
}
