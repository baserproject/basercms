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
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Log\Log;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Psr\Log\LogLevel;

/**
 * PluginsAdminServiceTest
 */
class PluginsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(PluginsScenario::class);
        $vars = $this->PluginsAdmin->getViewVarsForInstall($this->PluginsAdmin->get(1));
        $this->assertTrue(isset($vars['plugin']));
        $this->assertTrue(isset($vars['installStatusMessage']));
    }

    /**
     * test getViewVarsForUpdate
     */
    public function test_getViewVarsForUpdate()
    {
        $this->loadFixtureScenario(PluginsScenario::class);
        $vars = $this->PluginsAdmin->getViewVarsForUpdate($this->PluginsAdmin->get(1));
        $this->assertEquals([
            'plugin',
            'scriptNum',
            'scriptMessages',
            'dbVersion',
            'programVersion',
            'dbVerPoint',
            'programVerPoint',
            'availableVersion',
            'log',
            'coreDownloaded',
            'php',
            'isCore',
            'isWritableVendor',
            'isWritableComposerJson',
            'isWritableComposerLock',
            'isUpdatable'
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

    /**
     * test isRequireUpdate
     * @param string $programVersion
     * @param string|null $dbVersion
     * @param string|null $availableVersion
     * @param bool $expected
     * @dataProvider isRequireUpdateDataProvider
     */
    public function test_isRequireUpdate(string $programVersion, ?string $dbVersion, ?string $availableVersion, bool $expected)
    {
        $result = $this->PluginsAdmin->isRequireUpdate($programVersion, $dbVersion, $availableVersion);
        $this->assertEquals($expected, $result);
    }

    public static function isRequireUpdateDataProvider()
    {
        return [
            ['1.0.0', '1.0.0', '1.1.0', true],
            ['1.0.0', '1.0.0', '0.9.0', false],
            ['1.0.0', '1.0.0', '1.0.0', false],
            ['1.1.0', '1.0.0', '1.1.0', false],
            ['1.0.0', '1.0.0', null, false],
            ['invalid_version', '1.0.0', '1.1.0', false],
            ['1.0.0', 'invalid_version', '1.1.0', false],
            ['1.0.0', '1.0.0', 'invalid_version', false],
        ];
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        $vars = $this->PluginsAdmin->getViewVarsForAdd();
        $this->assertTrue(isset($vars['isPluginsDirWritable']));
    }

    /**
     * test isPluginsDirWritable
     */
    public function test_isPluginsDirWritable()
    {
        $result = $this->PluginsAdmin->isPluginsDirWritable();
        $this->assertTrue($result);
    }

}
