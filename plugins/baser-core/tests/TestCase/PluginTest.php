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

namespace BaserCore\Test\TestCase;

use App\Application;
use BaserCore\Plugin;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PluginTest
 * @package BaserCore\Test\TestCase
 * @property Plugin $Plugin
 */
class PluginTest extends BcTestCase
{
    /**
     * @var Plugin
     */
    public $Plugin;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.Plugins',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->application = new Application(CONFIG);
        $this->Plugin = new Plugin(['name' => 'BcBlog']);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->application);
        unset($this->Plugin);
        parent::tearDown();
    }

    /**
     * test bootstrap
     *
     * @return void
     */
    public function testBootStrap(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test loadPlugin
     *
     * @return void
     */
    public function testLoadPlugin(): void
    {
        $priority = 1;
        $plugin = $this->Plugin->getName();
        $this->assertTrue($this->Plugin->loadPlugin($this->application, $plugin, $priority));
    }

    /**
     * test middleware
     *
     * @return void
     */
    public function testMiddleware(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getAuthenticationService
     */
    public function testGetAuthenticationService(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test routes
     *
     * @return void
     */
    public function testRoutes(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test services
     *
     * @return void
     */
    public function testServices(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}