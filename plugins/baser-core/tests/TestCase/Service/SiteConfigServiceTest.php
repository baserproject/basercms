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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\SiteConfigService;
use josegonzalez\Dotenv\Loader;

class SiteConfigServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * @var SiteConfigService|null
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
        $this->SiteConfigs = new SiteConfigService();
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

}
