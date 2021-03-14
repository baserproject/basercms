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

namespace BcBlog\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcBlog\Plugin as BlogPlugin;
use Cake\Core\Plugin;

/**
 * Class BcPluginTest
 * @package BaserCore\Test\TestCase
 */
class BcPluginTest extends BcTestCase
{
    /**
     * @var \Cake\Core\PluginInterface
     */
    public $Plugin;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
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
        BcUtil::includePluginClass('BcBlog');
        $plugins = Plugin::getCollection();
        $this->Plugin = $plugins->create('BcBlog');
        $plugins->add($this->Plugin);
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

    public function testInstall()
    {
        $this->Plugin->install();
    }

    public function testUninstall()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
