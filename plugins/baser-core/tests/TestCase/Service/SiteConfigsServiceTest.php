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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;

/**
 * SiteConfigsServiceTest
 */
class SiteConfigsServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * @var SiteConfigsService|null
     */
    public $SiteConfigs = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteConfigs = $this->getService(SiteConfigsServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SiteConfigs);
        parent::tearDown();
    }

    /**
     * test construct
     * @return void
     */
    public function testConstruct(){
        $this->assertTrue(isset($this->SiteConfigs->SiteConfigs));
    }

    /**
     * test get
     */
    public function testGet()
    {
        $result = $this->SiteConfigs->get();
        $this->assertArrayHasKey('mode', $result);
        $this->assertArrayHasKey('site_url', $result);
        $this->assertArrayHasKey('ssl_url', $result);
        $this->assertArrayHasKey('admin_ssl', $result);
    }

    /**
     * test update
     */
    public function testUpdate()
    {
        $this->SiteConfigs->update([
            'admin_theme' => 'admin_second'
        ]);
        $this->assertEquals('admin_second', $this->SiteConfigs->getValue('admin_theme'));
        $path = ROOT . DS . 'config' . DS . '.env';
        copy($path, $path . '.bak');
        $this->SiteConfigs->update([
            'site_url' => 'http://hoge'
        ]);
        $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
        $dotenv->parse()
            ->putenv(true)
            ->toEnv(true)
            ->toServer(true);
        $this->assertEquals('http://hoge/', env('SITE_URL'));
        unlink($path);
        rename($path . '.bak', $path);
    }

    /**
     * test isWritableEnv
     */
    public function testIsWritableEnv(): void
    {
        $this->assertTrue($this->SiteConfigs->isWritableEnv());
    }

    /**
     * test putEnv
     */
    public function testPutEnv(): void
    {
        $path = ROOT . DS . 'config' . DS . '.env';
        copy($path, $path . '.bak');
        $this->SiteConfigs->putEnv('INSTALL_MODE', 'true');
        $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
        $dotenv->parse()
            ->putenv(true)
            ->toEnv(true)
            ->toServer(true);
        $this->assertTrue(filter_var(env('INSTALL_MODE'), FILTER_VALIDATE_BOOLEAN));
        $this->SiteConfigs->putEnv('BASERCMS', 'BASERCMS');
        $dotenv->parse()
            ->putenv(true)
            ->toEnv(true)
            ->toServer(true);
        $this->assertEquals('BASERCMS', filter_var(env('BASERCMS')));
        unlink($path);
        rename($path . '.bak', $path);
    }

    /**
     * test getModeList
     */
    public function testGetModeList()
    {
        $this->assertEquals([
            0 => 'ノーマルモード',
            1 => 'デバッグモード',
        ], $this->SiteConfigs->getModeList());
    }
    /**
     * testSetValue
     *
     * @return void
     */
    public function testSetValue(): void
    {
        $this->assertNotEmpty($this->SiteConfigs->setValue('admin_list_num', 30));
        $this->assertEquals(30, $this->SiteConfigs->getValue('admin_list_num'));
    }
    /**
     * testresetValue
     *
     * @return void
     */
    public function testResetValue(): void
    {
        $this->assertNotEmpty($this->SiteConfigs->resetValue('admin_list_num'));
        $this->assertEquals('', $this->SiteConfigs->getValue('admin_list_num'));
    }

    /**
     * test getVersion And clearCache
     */
    public function test_getVersionAndClearCache()
    {
        $this->assertEquals('3.0.6.1', $this->SiteConfigs->getVersion());
        $this->SiteConfigs->clearCache();
        $this->SiteConfigs->setValue('version', '5.0.0');
        $this->assertEquals('5.0.0', $this->SiteConfigs->getVersion());
    }

}
