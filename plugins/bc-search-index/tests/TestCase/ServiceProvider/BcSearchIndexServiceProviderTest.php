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

namespace BcSearchIndex\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Service\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use BcSearchIndex\ServiceProvider\BcSearchIndexServiceProvider;
use Cake\Core\Container;

/**
 * Class SearchIndexesServiceTest
 * @property BcSearchIndexServiceProvider $BcSearchIndexServiceProvider
 */
class BcSearchIndexServiceProviderTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcSearchIndexServiceProvider = new BcSearchIndexServiceProvider();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcSearchIndexServiceProvider);
        parent::tearDown();
    }

    /**
     * @test services
     * @return void
     */
    public function testServices()
    {
        $this->assertTrue(true);
        $container = new Container();
        $this->BcSearchIndexServiceProvider->services($container);
        $this->assertTrue($container->has(SearchIndexesServiceInterface::class));
        $this->assertTrue($container->has(SearchIndexesAdminServiceInterface::class));
    }

}
