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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\PluginsAdminService;
use BaserCore\Service\Admin\PluginsAdminServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Log\Log;
use Psr\Log\LogLevel;

/**
 * PluginsAdminServiceTest
 */
class PluginsAdminServiceTest extends BcTestCase
{

    /**
     * Fixtures
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Plugins'
    ];

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * PluginsAdminService
     * @var PluginsAdminService
     */
    public $PluginsAdmin;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PluginsAdmin = $this->getService(PluginsAdminServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PluginsAdmin);
    }

    /**
     * test getViewVarsForInstall
     */
    public function test_getViewVarsForInstall()
    {
        $vars = $this->PluginsAdmin->getViewVarsForInstall($this->PluginsAdmin->get(1));
        $this->assertTrue(isset($vars['plugin']));
        $this->assertTrue(isset($vars['installStatusMessage']));
    }

    /**
     * test getViewVarsForUpdate
     */
    public function test_getViewVarsForUpdate()
    {
        $vars = $this->PluginsAdmin->getViewVarsForUpdate($this->PluginsAdmin->get(1));
        $this->assertEquals([
            'plugin',
            'scriptNum',
            'scriptMessages',
            'siteVer',
            'baserVer',
            'siteVerPoint',
            'baserVerPoint',
            'log'
        ], array_keys($vars));
    }

    /**
     * test getUpdateLog
     */
    public function test_getUpdateLog()
    {
        if (file_exists(LOGS . 'update.log')) {
            rename(LOGS . 'update.log', LOGS . 'update.backup.log');
        }
        Log::write(LogLevel::INFO, 'test', 'update');
        $this->assertMatchesRegularExpression('/test\n$/', $this->PluginsAdmin->getUpdateLog());
        if (file_exists(LOGS . 'update.backup.log')) {
            rename(LOGS . 'update.backup.log', LOGS . 'update.log');
        }
    }

}
