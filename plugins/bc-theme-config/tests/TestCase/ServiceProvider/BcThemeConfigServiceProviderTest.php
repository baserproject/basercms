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

namespace BcThemeConfig\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use BcThemeConfig\ServiceProvider\BcThemeConfigServiceProvider;
use Cake\Core\Container;

/**
 * Class BcThemeConfigServiceProviderTest
 * @property BcThemeConfigServiceProvider $BcThemeConfigServiceProvider
 */
class BcThemeConfigServiceProviderTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcThemeConfigServiceProvider = new BcThemeConfigServiceProvider();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcThemeConfigServiceProvider);
        parent::tearDown();
    }

    /**
     * @test services
     * @return void
     */
    public function testServices()
    {
        $container = new Container();
        $this->BcThemeConfigServiceProvider->services($container);
        $this->assertTrue($container->has(ThemeConfigsServiceInterface::class));
    }
}
