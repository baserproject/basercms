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
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Filesystem\Folder;
use Cake\I18n\FrozenTime;
use Cake\Log\LogTrait;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use PDOException;

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

    /**
     * Constructor
     *
     * @checked
     * @noTodo
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
            'phpMemory' => intval(ini_get('memory_limit')),
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
            'apacheRewrite' => $rewriteInstalled
        ];
        $check = [
            'encodingOk' => (preg_match('/UTF-8/i', $status['encoding'])? true : false),
            'gdOk' => $status['phpGd'],
            'pdoOk' => $status['phpPdo'],
            'xmlOk' => $status['phpXml'],
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
            $dbDataPattern = Configure::read('BcApp.defaultTheme') . '.default';
        }
        if (strpos($dbDataPattern, '.') === false) {
            throw new BcException(__d('baser', 'データパターンの形式が不正です。'));
        }
        if (!$this->BcDatabase->constructionTable('BaserCore', 'default', $dbConfig)) {
            throw new BcException(__d('baser', 'コアテーブルの構築に失敗しました。'));
        }
        [$theme, $pattern] = explode('.', $dbDataPattern);
        try {
            if (!$this->BcDatabase->loadDefaultDataPattern($theme, $pattern)) {
                throw new BcException(__d('baser', 'コアの初期データのロードに失敗しました。'));
            }
        } catch (BcException $e) {
            throw new BcException(__d('baser', 'コアの初期データのロードに失敗しました。' . $e->getMessage()));
        }
        if (!$this->BcDatabase->initSystemData(['adminTheme' => $adminTheme])) {
            throw new BcException(__d('baser', 'システムデータの初期化に失敗しました。'));
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
     */
    public function getRealDbName(string $type, string $name)
    {
        if (preg_match('/^\//', $name)) {
            return $name;
        }
        if (!empty($type) && !empty($name)) {
            if ($type == 'sqlite') {
                return APP . 'db' . DS . 'sqlite' . DS . $name . '.db';
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
     */
    public function testConnectDb(array $config)
    {
        try {
            $this->BcDatabase->testConnectDb($config);
        } catch (PDOException $e) {
            throw $e;
        } catch (BcException $e) {
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
    public function setAdminEmail(string $email)
    {
        /* @var \BaserCore\Service\SiteConfigsService $siteConfigsService */
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        return $siteConfigsService->setValue('email', $email);
    }

    /**
     * セキュリティ用のキーを生成する
     *
     * @param int $length
     * @return string キー
     * @checked
     * @noTodo
     */
    public function setSecuritySalt($length = 40): string
    {
        $keyset = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randkey = "";
        for($i = 0; $i < $length; $i++) {
            $randkey .= substr($keyset, rand(0, strlen($keyset) - 1), 1);
        }
        Configure::write('Security.salt', $randkey);
        return $randkey;
    }

    /**
     * 初期ユーザーを登録する
     *
     * @param array $user
     * @return \Cake\Datasource\EntityInterface
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     */
    public function addDefaultUser(array $user, $securitySalt = '')
    {
        if ($securitySalt) {
            Configure::write('Security.salt', $securitySalt);
        }
        $user = array_merge([
            'name' => '',
            'real_name_1' => $user['name'],
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
     */
    public function executeDefaultUpdates(): bool
    {
        $result = true;
        if (!$this->_updatePluginStatus()) {
            $this->log(__d('baser', 'プラグインの有効化に失敗しました。'));
            $result = false;
        }
        if (!$this->_updateContents()) {
            $this->log(__d('baser', 'コンテンツの更新に失敗しました。'));
            $result = false;
        }
        return $result;
    }

    /**
     * コンテンツの作成日を更新する
     *
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _updateContents(): bool
    {
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contents = $contentsTable->find()->all();
        $result = true;
        foreach($contents as $content) {
            $content->created_date = new FrozenTime();
            if (!$contentsTable->save($content)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * プラグインのステータスを更新する
     *
     * @return boolean
     * @checked
     * @noTodo
     */
    protected function _updatePluginStatus(): bool
    {
        $this->BcDatabase->truncate('plugins');
        $version = BcUtil::getVersion();
        $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
        $priority = intval($pluginsTable->getMax('priority')) + 1;
        $corePlugins = Configure::read('BcApp.corePlugins');
        $result = true;
        foreach($corePlugins as $corePlugin) {
            $plugin = $pluginsTable->getPluginConfig($corePlugin);
            $plugin = $pluginsTable->patchEntity($plugin, [
                'name' => $corePlugin,
                'version' => $version,
                'status' => true,
                'db_inited' => false,
                'priority' => $priority
            ]);
            if (!$pluginsTable->save($plugin)) {
                $result = false;
            }
            $priority++;
        }
        return $result;
    }

    /**
     * コアプラグインをインストールする
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function installCorePlugin(string $dbDataPattern): bool
    {
        $result = true;
        $corePlugins = Configure::read('BcApp.corePlugins');
        foreach($corePlugins as $corePlugin) {
            if (!$this->installPlugin($corePlugin, $dbDataPattern)) {
                $this->log(sprintf(__d('baser', 'コアプラグイン %s のインストールに失敗しました。'), $corePlugin));
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
     */
    public function installPlugin($name, $dbDataPattern = '')
    {
        BcUtil::clearAllCache();
        // TODO ucmitz 引数となる $dbDataPattern が利用できる仕様となっていない
        /* @var BcPlugin $plugin */
        $plugin = Plugin::isLoaded($name);
        if(!$plugin) $plugin = Plugin::getCollection()->create($name);
        return $plugin->install();

        $paths = App::path('Plugin');
        $exists = false;
        foreach($paths as $path) {
            if (file_exists($path . $name)) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            return false;
        }

        $this->Plugin = ClassRegistry::init('Plugin');
        $data = $this->Plugin->find('first', ['conditions' => ['name' => $name]]);
        $title = '';

        if (empty($data['Plugin']['db_inited'])) {
            $initPath = $path . $name . DS . 'Config' . DS . 'init.php';
            if (file_exists($initPath)) {
                $this->initPlugin($initPath, $dbDataPattern);
            }
        }
        $configPath = $path . $name . DS . 'config.php';
        if (file_exists($configPath)) {
            include $configPath;
        }

        if (empty($title)) {
            if (!empty($data['Plugin']['title'])) {
                $title = $data['Plugin']['title'];
            } else {
                $title = $name;
            }
        }

        if ($data) {
            // 既にインストールデータが存在する場合は、DBのバージョンは変更しない
            $data = array_merge($data['Plugin'], [
                'name' => $name,
                'title' => $title,
                'status' => true,
                'db_inited' => true
            ]);
            $this->Plugin->set($data);
        } else {
            $corePlugins = Configure::read('BcApp.corePlugins');
            if (in_array($name, $corePlugins)) {
                $version = BcUtil::getVersion();
            } else {
                $version = BcUtil::getVersion($name);
            }

            $priority = intval($this->Plugin->getMax('priority')) + 1;
            $data = ['Plugin' => [
                'name' => $name,
                'title' => $title,
                'status' => true,
                'db_inited' => true,
                'version' => $version,
                'priority' => $priority
            ]];
            $this->Plugin->create($data);
        }

        // データを保存
        if ($this->Plugin->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * プラグインを初期化
     *
     * @param $_path
     */
    public function initPlugin($_path, $dbDataPattern = '')
    {
        if ($dbDataPattern) {
            $_SESSION['dbDataPattern'] = $dbDataPattern;
        }
        ClassRegistry::flush();
        if (file_exists($_path)) {
            try {
                set_time_limit(0);
                include $_path;
            } catch (Exception $e) {
                $this->log($e->getMessage());
            }
        }
    }

    /**
     * インストール設定ファイルを生成する
     *
     * @param array $dbConfig
     * @param string $securitySalt
     * @return boolean
     * @checked
     * @noTodo
     */
    public function createInstallFile(array $dbConfig, string $securitySalt): bool
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

        $basicSettings = [
            'Security.salt' => $securitySalt
        ];

        $installCoreData = [
            '<?php',
            '// created by BcInstaller',
            'return ['
        ];
        foreach($basicSettings as $key => $value) {
            $installCoreData[] = '    \'' . $key . '\' => \'' . $value . '\',';
        }
        $installCoreData[] = '    \'Datasources.default\' => [';
        foreach($dbConfig as $key => $value) {
            $installCoreData[] = '        \'' . $key . '\' => \'' . $value . '\',';
        }
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
     */
    public function createDefaultFiles(): bool
    {
        $dirs = ['blog', 'editor', 'theme_configs'];
        $path = WWW_ROOT . 'files' . DS;
        $Folder = new Folder();
        $result = true;
        foreach($dirs as $dir) {
            if (!is_dir($path . $dir)) {
                if (!$Folder->create($path . $dir, 0777)) {
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
     */
    public function deployEditorTemplateImage(): bool
    {
        $path = WWW_ROOT . 'files' . DS . 'editor' . DS;
        if (!is_dir($path)) {
            $Folder = new Folder();
            $Folder->create($path, 0777);
        }
        $pluginPath = BcUtil::getPluginPath(Configure::read('BcApp.defaultAdminTheme')) . DS;
        $src = $pluginPath . DS . 'webroot' . DS . 'img' . DS . 'admin' . DS . 'ckeditor' . DS;
        $Folder = new Folder($src);
        $files = $Folder->read(true, true);
        $result = true;
        if (!empty($files[1])) {
            foreach($files[1] as $file) {
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
     */
    protected function _getDbSource(): array
    {
        // TODO uctmiz 未実装
        return ['mysql' => 'MySQL'];

        /* DBソース取得 */
        $dbsource = [];
        $folder = new Folder();
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
            $dbFolderPath = APP . 'db' . DS . 'sqlite';
            if (is_writable(dirname($dbFolderPath)) && $folder->create($dbFolderPath, 0777)) {
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
     */
    public function getAllDefaultDataPatterns(): array
    {
        $themesService = $this->getService(ThemesServiceInterface::class);
        // コア
        $patterns = $themesService->getDefaultDataPatterns();
        // 外部テーマ
        $Folder = new Folder(BASER_THEMES);
        $files = $Folder->read(true, true, false);
        foreach($files[0] as $theme) {
            $configPath = BASER_PLUGINS . $theme . DS . 'config.php';
            if (!file_exists($configPath)) continue;
            $config = include $configPath;
            if (!isset($config['type']) || $config['type'] !== 'theme') continue;
            $patterns = array_merge($patterns, $themesService->getDefaultDataPatterns($theme));
        }
        return $patterns;
    }

}
