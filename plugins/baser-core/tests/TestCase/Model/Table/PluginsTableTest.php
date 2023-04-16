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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\PluginsTable;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validator;

/**
 * Class PluginsTableTest
 * @property PluginsTable $Plugins
 */
class PluginsTableTest extends BcTestCase
{

    /**
     * @var PluginsTable
     */
    public $Plugins;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Plugins = $this->getTableLocator()->get('BaserCore.Plugins');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Plugins);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertIsBool($this->Plugins->hasBehavior('Timestamp'));
    }

    /**
     * testInstall and testUninstall
     */
    public function testInstallAndUninstall()
    {
        // test Install
        $this->Plugins->install('BcTest');
        $plugin = $this->Plugins->find()->where(['name' => 'BcTest'])->first();
        $this->assertEquals(5, $plugin->priority);
        // test Uninstall
        $this->Plugins->uninstall('BcTest');
        $this->assertEquals(5, $this->Plugins->find()->count());
    }

    /**
     * testGetPluginConfig
     */
    public function testGetPluginConfig()
    {
        $plugin = $this->Plugins->getPluginConfig('BaserCore');
        $this->assertEquals('BaserCore', $plugin->name);
    }

    /**
     * testDetach
     */
    public function testDetach()
    {
        $plugin = 'BcBlog';
        $this->assertFalse($this->Plugins->detach(''));
        $this->Plugins->detach($plugin);
        $this->assertFalse($this->Plugins->find()->where(['name' => $plugin])->first()->status);
    }
    /**
     * test attach
     */
    public function testAttach()
    {
        $plugin = 'BcBlog';
        $this->Plugins->detach($plugin);
        $this->Plugins->attach($plugin);
        $this->assertTrue($this->Plugins->find()->where(['name' => $plugin])->first()->status);
    }

    /**
     * Test validationDefault
     * @return void
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($isValid, $data)
    {
        $validator = $this->Plugins->validationDefault(new Validator());
        $validator->setProvider('table', $this->Plugins);
        if ($isValid) {
            $this->assertEmpty($validator->validate($data));
        } else {
            $this->assertNotEmpty($validator->validate($data));
        }
    }

    public function validationDefaultDataProvider()
    {
        $exceedMax = "123456789012345678901234567890123456789012345678901234567890"; // 60文字
        return [
            // 妥当な例
            [true, ['name' => 'aA-_1', 'title' => 'testtest']],
            // nameがnull
            [false, ['name' => '', 'title' => 'testtest']],
            // nameに許可されない文字がある
            [false, ['name' => '@@@@@', 'title' => 'testtest']],
            // nameの文字数が長い
            [false, ['name' => $exceedMax, 'title' => 'testtest']],
            // titleの文字数が長い
            [false, ['name' => 'aA-_1', 'title' => $exceedMax]],
            // 重複
            [false, ['name' => 'BcBlog', 'title' => 'testtest']],
        ];
    }

    /**
     * test update
     */
    public function test_update()
    {
        $this->Plugins->update('', '6.0.0');
        $this->assertEquals('6.0.0', BcUtil::getDbVersion());
        PluginFactory::make(['name' => 'BcSample', 'version' => '1.0.0'])->persist();
        $this->Plugins->update('BcSample', '6.0.0');
        $this->assertEquals('6.0.0', BcUtil::getDbVersion('BcSample'));
    }

}
