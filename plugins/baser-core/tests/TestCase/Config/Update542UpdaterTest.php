<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.2
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Config;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUpdateLog;
use Cake\Core\Plugin;

/**
 * 5.4.2 アップデーター（install.php への cacheMetadata 追記）のテスト
 */
class Update542UpdaterTest extends BcTestCase
{

    /**
     * アップデータースクリプトのパス
     */
    private string $updaterPath;

    /**
     * テスト用の install.php
     */
    private string $installFile;

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->updaterPath = Plugin::path('BaserCore') . 'config' . DS . 'update' . DS . '5.4.2' . DS . 'updater.php';
        $this->installFile = TMP . 'install_updater_test.php';
        BcUpdateLog::clear();
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        if (file_exists($this->installFile)) unlink($this->installFile);
        BcUpdateLog::clear();
        parent::tearDown();
    }

    /**
     * アップデーターを対象ファイルを差し替えて実行する
     */
    private function runUpdater(): void
    {
        $installFile = $this->installFile;
        include $this->updaterPath;
    }

    /**
     * インストーラが生成した形式の install.php に cacheMetadata が追記される
     */
    public function testAddsCacheMetadataToGeneratedInstallFile()
    {
        file_put_contents($this->installFile, <<<'PHP'
<?php
// created by BcInstaller
return [
    'Datasources.default' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Mysql',
        'database' => 'basercms',
        'log' => filter_var(env('SQL_LOG', false), FILTER_VALIDATE_BOOLEAN)
    ],
    'Datasources.test' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Mysql',
        'database' => 'test_basercms',
        'log' => filter_var(env('SQL_LOG', false), FILTER_VALIDATE_BOOLEAN)
    ]
];
PHP);

        $this->runUpdater();

        $config = include $this->installFile;
        $this->assertTrue($config['Datasources.default']['cacheMetadata']);
        $this->assertTrue($config['Datasources.test']['cacheMetadata']);
        $this->assertSame('basercms', $config['Datasources.default']['database'], '他の設定が壊れています');
        $this->assertSame(2, substr_count(file_get_contents($this->installFile), "'cacheMetadata' => true"));
    }

    /**
     * 既に cacheMetadata がある場合は何もしない（2 回実行しても重複しない）
     */
    public function testDoesNothingWhenCacheMetadataExists()
    {
        file_put_contents($this->installFile, <<<'PHP'
<?php
return [
    'Datasources.default' => [
        'driver' => 'Cake\Database\Driver\Mysql',
        'cacheMetadata' => false,
    ],
];
PHP);
        $before = file_get_contents($this->installFile);

        $this->runUpdater();

        $this->assertSame($before, file_get_contents($this->installFile), '既存の cacheMetadata 設定が書き換えられています');
    }

    /**
     * 手書きで log 行が無い install.php でも追記される
     */
    public function testAddsCacheMetadataToHandWrittenInstallFile()
    {
        file_put_contents($this->installFile, <<<'PHP'
<?php
return [
    'Datasources.default' => ['driver' => 'Cake\Database\Driver\Mysql', 'database' => 'basercms'],
];
PHP);

        $this->runUpdater();

        $config = include $this->installFile;
        $this->assertTrue($config['Datasources.default']['cacheMetadata']);
        $this->assertSame('basercms', $config['Datasources.default']['database']);
    }

    /**
     * install.php が無い場合はログを残してスキップする
     */
    public function testLogsWhenInstallFileIsMissing()
    {
        $this->runUpdater();
        $this->assertNotEmpty(BcUpdateLog::get());
    }

}
