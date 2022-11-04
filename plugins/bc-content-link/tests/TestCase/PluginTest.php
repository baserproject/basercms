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

namespace BcContentLink\Test\TestCase;

use BcContentLink\Plugin;
use BcContentLink\Service\ContentLinksServiceInterface;
use Cake\Core\Container;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PluginTest
 * @property Plugin $Plugin
 */
class PluginTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Plugin = new Plugin(['name' => 'BcContentLink']);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }


    /**
     * test services
     *
     * @return void
     */
    public function testServices(): void
    {
        $container = new Container();
        $this->Plugin->services($container);
        $this->assertTrue($container->has(ContentLinksServiceInterface::class));
    }

}
