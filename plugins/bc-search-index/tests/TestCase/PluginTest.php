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

namespace BcSearchIndex\Test\TestCase;

use BcSearchIndex\Plugin;
use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Service\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Core\Container;

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
        $this->Plugin = new Plugin(['name' => 'BcSearchIndex']);
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
        $this->assertTrue($container->has(SearchIndexesServiceInterface::class));
        $this->assertTrue($container->has(SearchIndexesAdminServiceInterface::class));
    }

}
