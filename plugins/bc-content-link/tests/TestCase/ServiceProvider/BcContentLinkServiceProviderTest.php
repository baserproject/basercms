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

namespace BcContentLink\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Service\ContentLinksServiceInterface;
use BcContentLink\ServiceProvider\BcContentLinkServiceProvider;
use Cake\Core\Container;

/**
 * Class BcContentLinkServiceProviderTest
 * @property BcContentLinkServiceProvider $BcContentLinkServiceProvider
 */
class BcContentLinkServiceProviderTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcContentLinkServiceProvider = new BcContentLinkServiceProvider();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcContentLinkServiceProvider);
        parent::tearDown();
    }

    /**
     * Test services
     */
    public function testServices()
    {
        $container = new Container();
        $this->BcContentLinkServiceProvider->services($container);
        $this->assertTrue($container->has(ContentLinksServiceInterface::class));
    }

}
