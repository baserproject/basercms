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

namespace BcInstaller\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\BcPlugin;
use BaserCore\Error\BcException;
use BaserCore\Model\Entity\SiteConfig;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\PermissionGroupsService;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Driver\Sqlite;
use Cake\Filesystem\Folder;
use Cake\I18n\FrozenTime;
use Cake\Log\LogTrait;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use PDO;
use PDOException;
use SQLite3;

/**
 * InstallationsService
 * @property BcDatabaseService $BcDatabase
 */
class InstallationsService implements InstallationsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use LogTrait;
    use MailerAwareTrait;

    /**
     * BcDatabase Service
     * @var BcDatabaseServiceInterface|BcDatabaseService
     */
    public BcDatabaseServiceInterface|BcDatabaseService $BcDatabase;

    /**
     * Constructor
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BcDatabase = $this->getService(BcDatabaseServiceInterface::class);
    }

    /**
     * 環境情報をチェックする
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function checkEnv(): array
    {
        if (function_exists('apache_get_modules')) {
            $rewriteInstalled = in_array('mod_rewrite', apache_get_modules());
        } else {
            $rewriteInstalled = -1;
        }

        $info = [
            'configDir' => ROOT . DS . 'config',
            'pluginDir' => ROOT . DS . 'plugins',
            'filesDir' => WWW_ROOT . 'files',
            'tmpDir' => TMP,
            'dbDir' => ROOT . DS . 'db',
            'requirePhpVersion' => Configure::read('BcRequire.phpVersion'),
            'requirePhpMemory' => Configure::read('BcRequire.phpMemory')
        ];

        $status = [
            'encoding' => mb_internal_encoding(),
            'phpVersion' => phpversion(),
            'phpMemory' => $this->_getMemoryLimit(),
            'safeModeOff' => !ini_get('safe_mode'),
            'configDirWritable' => is_writable($info['configDir']),
            'pluginDirWritable' => is_writable($info['pluginDir']),
            'filesDirWritable' => is_writable($info['filesDir']),
            'tmpDirWritable' => is_writable($info['tmpDir']),
            'dbDirWritable' => is_writable($info['dbDir']),
            'phpActualVersion' => preg_replace('/[a-z-]/', '', phpversion()),
            'phpGd' => extension_loaded('gd'),
            'phpPdo' => extension_loaded('pdo'),
            'phpXml' => extension_loaded('xml'),
            'phpZip' => extension_loaded('zip'),
            'apacheRewrite' => $rewriteInstalled
        ];
        $check = [
            'encodingOk' => (preg_match('/UTF-8/i', $status['encoding'])? true : false),
            'gdOk' => $status['phpGd'],
            'pdoOk' => $status['phpPdo'],
            'xmlOk' => $status['phpXml'],
            'zipOk' => $status['phpZip'],
            'phpVersionOk' => version_compare(preg_replace('/[a-z-]/', '', $status['phpVersion']), $info['requirePhpVersion'], '>='),
            'phpMemoryOk' => ((($status['phpMemory'] >= $info['requirePhpMemory']) || $status['phpMemory'] == -1) === true)
        ];

        $check['blRequirementsMet'] = (
            $status['phpXml'] &&
            $status['phpGd'] &&
            $status['tmpDirWritable'] &&
            $status['configDirWritable'] &&
            $check['phpVersionOk']
        );

        if (!$status['configDirWritable']) {
            @chmod($info['configDir'], 0777);
            $status['configDirWritable'] = is_writable($info['configDir']);
        }
        if (!$status['pluginDirWritable']) {
            @chmod($info['pluginDir'], 0777);
            $status['pluginDirWritable'] = is_writable($info['pluginDir']);
        }
        if (!$status['filesDirWritable']) {
            @chmod($info['filesDir'], 0777);
            $status['filesDirWritable'] = is_writable($info['filesDir']);
        }
        if (!$status['tmpDirWritable']) {
            @chmod($info['tmpDir'], 0777);
            $status['tmpDirWritable'] = is_writable($info['tmpDir']);
        }
        if (!$status['dbDirWritable']) {
            @chmod($info['dbDir'], 0777);
            $status['dbDirWritable'] = is_writable($info['dbDir']);
        }

        return $info + $status + $check;
    }

	/**
	 * memory_limit を取得する
	 * @return int
     * @checked
     * @noTodo
     * @unitTest
	 */
	protected function _getMemoryLimit ()
	{
		$size = ini_get('memory_limit');
		switch (substr ($size, -1)) {
			case 'M': case 'm': return (int) $size;
			case 'G': case 'g': return (int) $size * 1024;
			default: return (int) $size;
		}
	}

    /**
     * baserCMSコアのデータベースを構築する
     *
     * @param array $dbConfig データベース設定名
     * @param string $dbDataPattern データパターン
     * @param string $adminTheme
     * @return boolean
     * @checked
     * @noTodo
     */
    public function constructionDb(array $dbConfig, string $dbDataPattern = '', string $adminTheme = ''): bool
    {
        if (!$dbDataPattern) {
            $dbDataPattern = Configure::read('BcApp.coreFrontTheme') . '.default';
        }
        if (strpos($dbDataPattern, '.') === false) {
            throw new BcException(__d('baser_core', 'データパターンの形式が不正です。'));
        }
        if (!$this->BcDatabase->constructionTable('BaserCore', 'default', $dbConfig)) {
            throw new BcException(__d('baser_core', 'コアテーブルの構築に失敗しました。'));
        }

        try {
            $this->installCorePlugin();
        } catch (\Throwable $e) {
            throw new BcException(__d('baser_core', 'コアプラグインのインストールに失敗しました。'));
        }

        try {
            [$theme, $pattern] = explode('.', $dbDataPattern);
            if (!$this->BcDatabase->loadDefaultDataPattern($theme, $pattern)) {
                throw new BcException(__d('baser_core', 'コアの初期データのロードに失敗しました。'));
            }
        } catch (\Throwable $e) {
            throw new BcException(__d('baser_core', 'コアの初期データのロードに失敗しました。' . $e->getMessage()));
        }

        if (!$this->BcDatabase->initSystemData(['theme' => $theme, 'adminTheme' => $adminTheme])) {
            throw new BcException(__d('baser_core', 'システムデータの初期化に失敗しました。'));
        }
        $datasource = strtolower(str_replace('Cake\\Database\\Driver\\', '', $dbConfig['driver']));
        if ($datasource === 'postgres') {
            $this->BcDatabase->updateSequence();
        }
        return true;
    }

    /**
     * 実際の設定用のDB名を取得する
     *
     * @param string $type
     * @param string $name
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRealDbName(string $type, string $name)
    {
        if (preg_match('/^\//', $name)) {
            return $name;
        }
        if (!empty($type) && !empty($name)) {
            if ($type == 'sqlite') {
                return ROOT . DS . 'db' . DS . 'sqlite' . DS . $name . '.db';
            }
        }
        return $name;
    }

    /**
     * DBへの接続テストを行う
     *
     * @param array $config
     * @throws PDOException
     * @throws BcException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function testConnectDb(array $config)
    {
        try {
            $this->BcDatabase->testConnectDb($config);
        } catch (PDOException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * サイト基本設定に管理用メールアドレスを登録する
     *
     * @param string $email
     * @return SiteConfig|false
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function setAdminEmailAndVersion(string $email)
    {
        /* @var \BaserCore\Service\SiteConfigsService $siteConfigsService */
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        return ($siteConfigsService->setValue('email', $email) &&
            $siteConfigsService->setValue('version', BcUtil::getVersion()));
    }

    /**
     * 初期ユーザーを登録する
     *
     * @param array $user
     * @return \Cake\Datasource\EntityInterface
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addDefaultUser(array $user)
    {
        $user = array_merge([
            'name' => '',
            'real_name_1' => preg_replace('/@.+$/', '', $user['email']),
            'email' => '',
            'password_1' => '',
            'password_2' => '',
            'user_groups' => ['_ids' => [1]]
        ], $user);
        /* @var \BaserCore\Service\UsersService $usersService */
        $usersService = $this->getService(UsersServiceInterface::class);
        try {
            return $usersService->create($user);
        } catch (PersistenceFailedException $e) {
            throw $e;
        }
    }

    /**
     * サイト名を登録する
     *
     * @param string $name
     * @return \Cake\Datasource\EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSiteName(string $name)
    {
        /* @var \BaserCore\Service\SitesService $sitesService */
        $sitesService = $this->getService(SitesServiceInterface::class);
        return $sitesService->update($sitesService->get(1), [
            'display_name' => $name,
            'title' => $name
        ]);
    }

    /**
     * データベースのデータに初期更新を行う
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function executeDefaultUpdates(): bool
    {
        /** @var SearchIndexesServiceInterface $searchIndexesService */
        $searchIndexesService = $this->getService(SearchIndexesServiceInterface::class);
        $searchIndexesService->reconstruct();
        return true;
    }

    /**
     * コアプラグインをインストールする
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function installCorePlugin(): bool
    {
        $result = true;
        $corePlugins = Configure::read('BcApp.defaultInstallCorePlugins');

        // BcSearchIndex についてインストール時に検索インデックスの構築を行うため、最後に移動
        $key = array_search('BcSearchIndex', $corePlugins);
        unset($corePlugins[$key]);
        $corePlugins[] = 'BcSearchIndex';

        foreach($corePlugins as $corePlugin) {
            if (!$this->installPlugin($corePlugin)) {
                $this->log(sprintf(__d('baser_core', 'コアプラグイン %s のインストールに失敗しました。'), $corePlugin));
                $result = false;
            }
        }
        return $result;
    }

    /**
     * プラグインをインストールする
     *
     * @param string $name
     * @param string $dbDataPattern
     * @return boolean
     * @checked
     * @noTodo
     */
    public function installPlugin($name)
    {
        BcUtil::clearAllCache();
        /* @var BcPlugin $plugin */
        $plugin = Plugin::isLoaded($name);
        if(!$plugin) $plugin = Plugin::getCollection()->create($name);
        return $plugin->install();
    }

    /**
     * インストール設定ファイルを生成する
     *
     * @param array $dbConfig
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createInstallFile(array $dbConfig): bool
    {
		if (!is_writable(ROOT . DS . 'config' . DS)) {
			return false;
		}
        $installFileName = ROOT . DS . 'config' . DS . 'install.php';

        $dbConfig = array_merge([
            'className' => Connection::class,
            'driver' => '',
            'host' => 'localhost',
            'port' => '',
            'username' => 'dummy',
            'password' => 'dummy',
            'database' => 'dummy',
            'prefix' => '',
            'schema' => ''
        ], $dbConfig);
        // 入力された文字列よりPHPプログラムファイルを生成するため'(シングルクオート)をサニタイズ
        foreach($dbConfig as $key => $value) {
            $dbConfig[$key] = addcslashes($value, '\'\\');
        }

        $installCoreData = [
            '<?php',
            '// created by BcInstaller',
            'return ['
        ];
        $installCoreData[] = '    \'Datasources.default\' => [';
        foreach($dbConfig as $key => $value) {
            if($key === 'datasource' || $key === 'dataPattern') continue;
            $installCoreData[] = '        \'' . $key . '\' => \'' . $value . '\',';
        }
        $installCoreData[] = '        \'log\' => filter_var(env(\'SQL_LOG\', false), FILTER_VALIDATE_BOOLEAN)';
        $installCoreData[] = '    ],';
        $installCoreData[] = '    \'Datasources.test\' => [';
        foreach($dbConfig as $key => $value) {
            if($key === 'database') {
                if(str_replace('\\\\', '\\', $dbConfig['driver']) === Sqlite::class) {
                    $value = dirname($value) . DS . 'test_' . basename($value);
                } else {
                    $value = 'test_' . $value;
                }
            }
            if($key === 'datasource' || $key === 'dataPattern') continue;
            $installCoreData[] = '        \'' . $key . '\' => \'' . $value . '\',';
        }
        $installCoreData[] = '        \'log\' => filter_var(env(\'SQL_LOG\', false), FILTER_VALIDATE_BOOLEAN)';
        $installCoreData[] = '    ]';
        $installCoreData[] = '];';

        if (file_put_contents($installFileName, implode("\n", $installCoreData))) {
            return chmod($installFileName, 0666);
        } else {
            return false;
        }
    }

    /**
     * アップロード用初期フォルダを作成する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createDefaultFiles(): bool
    {
        $dirs = ['blog', 'editor', 'theme_configs'];
        $path = WWW_ROOT . 'files' . DS;
        $result = true;
        foreach($dirs as $dir) {
            if (!is_dir($path . $dir)) {
                $Folder = new BcFolder($path . $dir);
                if (!$Folder->create()) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * エディタテンプレート用のアイコン画像をデプロイ
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deployEditorTemplateImage(): bool
    {
        $path = WWW_ROOT . 'files' . DS . 'editor' . DS;
        if (!is_dir($path)) {
            (new BcFolder($path))->create();
        }
        $pluginPath = BcUtil::getPluginPath(Configure::read('BcApp.coreAdminTheme')) . DS;
        $src = $pluginPath . DS . 'webroot' . DS . 'img' . DS . 'admin' . DS . 'ckeditor' . DS;
        $Folder = new BcFolder($src);
        $files = $Folder->getFiles();
        $result = true;
        if (!empty($files)) {
            foreach($files as $file) {
                if (copy($src . $file, $path . $file)) {
                    @chmod($path . $file, 0666);
                } else {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * 利用可能なデータソースを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getDbSource(): array
    {
        /* DBソース取得 */
        $dbsource = [];
        $pdoDrivers = PDO::getAvailableDrivers();
        /* MySQL利用可否 */
        if (in_array('mysql', $pdoDrivers)) {
            $dbsource['mysql'] = 'MySQL';
        }
        /* PostgreSQL利用可否 */
        if (in_array('pgsql', $pdoDrivers)) {
            $dbsource['postgres'] = 'PostgreSQL';
        }
        /* SQLite利用可否チェック */
        if (in_array('sqlite', $pdoDrivers) && extension_loaded('sqlite3') && class_exists('SQLite3')) {
            $dbFolderPath = ROOT . DS . 'db' . DS . 'sqlite';
            if (is_writable(dirname($dbFolderPath)) && (new BcFolder($dbFolderPath))->create()) {
                $info = SQLite3::version();
                if (version_compare($info['versionString'], Configure::read('BcRequire.winSQLiteVersion'), '>')) {
                    $dbsource['sqlite'] = 'SQLite';
                }
            }
        }
        return $dbsource;
    }

    /**
     * 全てのテーマの初期データのリストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAllDefaultDataPatterns(): array
    {
        $themesService = $this->getService(ThemesServiceInterface::class);
        $paths = [
            BASER_THEMES,
            ROOT . DS . 'vendor' . DS . 'baserproject' . DS
        ];
        $patterns = [];
        foreacH($paths as $path) {
            $Folder = new BcFolder($path);
            $files = $Folder->getFolders(['full'=>true]);
            foreach($files as $dir) {
                $theme = basename($dir);
                $configPath = $dir . DS . 'config.php';

                if (!file_exists($configPath)) continue;
                $config = include $configPath;

                if (!isset($config['type']) || $config['type'] !== 'Theme') continue;
                $patterns = array_merge($patterns, $themesService->getDefaultDataPatterns($theme));
            }
        }
        return $patterns;
    }

    /**
     * インストール完了メールを送信
     *
     * @param array $email
     * @checked
     * @noTodo
     * @unitTest
     */
    public function sendCompleteMail(array $postData)
    {
        try {
            $this->getMailer('BcInstaller.Admin/Installer')->send('installed', [$postData['admin_email']]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * アクセスルールを構築する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function buildPermissions()
    {
        /** @var PermissionGroupsService $permissionGroupsService */
        $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
        $permissionGroupsService->buildAll();
    }

}
