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

namespace BaserCore\Service;

use BaserCore\Database\Schema\BcSchema;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Event\EventManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Log\LogTrait;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\View;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Migrations\CakeAdapter;
use Migrations\ConfigurationTrait;
use Migrations\Migrations;
use PDO;
use PDOException;
use Phinx\Db\Adapter\AdapterFactory;

/**
 *
 */
class BcDatabaseService implements BcDatabaseServiceInterface
{

    /**
     * Trait
     */
    use LogTrait;
    use BcContainerTrait;
    use ConfigurationTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initAdapter();
    }

    /**
     * アダプターを初期化する
     *
     * アダプターはテーブルのマイグレーションに利用する
     *
     * @checked
     * @noTodo
     */
    protected function initAdapter()
    {
        if(!BcUtil::isInstalled()) return;
        $db = TableRegistry::getTableLocator()
            ->get('BaserCore.App')
            ->getConnection();
        $config = $db->config();
        $options = [
            'adapter' => $this->getAdapterName($config['driver']),
            'host' => $config['host'] ?? null,
            'user' => $config['username'] ?? null,
            'pass' => $config['password'] ?? null,
            'port' => $config['port'] ?? null,
            'name' => $config['database'],
            'charset' => $config['encoding'] ?? null,
            'unix_socket' => $config['unix_socket'] ?? null,
        ];
        $factory = AdapterFactory::instance();
        $adapter = $factory->getAdapter($options['adapter'], $options);
        /* @var PDO $pdo */
        $pdo = $db->getDriver()->getConnection();
        $adapter->setConnection($pdo);
        $adapter = new CakeAdapter($adapter, $db);
        $this->_adapter = $adapter;
    }

    /**
     * マイグレーション用のテーブルオブジェクトを取得する。
     *
     * @param string $tableName
     * @return \Migrations\Table
     * @checked
     * @noTodo
     */
    public function getMigrationsTable(string $tableName)
    {
        return new \Migrations\Table($tableName, [], $this->_adapter);
    }

    /**
     * テーブルにカラムを追加する
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $type
     * @param array $options
     * @return bool
     */
    public function addColumn(
        string $tableName,
        string $columnName,
        string $type,
        array $options = []
    )
    {
        $options = array_merge([
            'default' => null,
            'null' => true,
        ], $options);
        $table = $this->getMigrationsTable($tableName);
        $table->addColumn($columnName, $type, $options);
        $table->update();
        BcUtil::clearModelCache();
        return true;
    }

    /**
     * テーブルよりカラムを削除する
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    public function removeColumn(string $tableName, string $columnName)
    {
        $table = $this->getMigrationsTable($tableName);
        $table->removeColumn($columnName);
        $table->update();
        BcUtil::clearModelCache();
        return true;
    }

    /**
     * テーブルのカラム名を変更する
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $newColumnName
     * @return bool
     */
    public function renameColumn(
        string $tableName,
        string $columnName,
        string $newColumnName
    )
    {
        $table = $this->getMigrationsTable($tableName);
        $table->renameColumn($columnName, $newColumnName);
        $table->update();
        BcUtil::clearModelCache();
        return true;
    }

    /**
     * テーブルを作成する
     *
     * @param string $tableName
     * @param array $columns
     * @return bool
     */
    public function createTable(string $tableName, array $columns = [])
    {
        $table = $this->getMigrationsTable($tableName);
        $table->create();
        if($columns) {
            foreach($columns as $key => $column) {
                $type = $column['type'];
                unset($column['type']);
                $table->addColumn($key, $type, $column);
            }
        }
        $table->update();
        BcUtil::clearModelCache();
        $this->clearAppTableList();
        return true;
    }

    /**
     * テーブルの存在チェックを行う
     *
     * @param string $tableName
     * @return bool
     */
    public function tableExists(string $tableName): bool
    {
        $tables = TableRegistry::getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        return in_array($tableName, $tables);
    }

    /**
     * テーブルを削除する
     *
     * @param string $tableName
     * @return bool
     */
    public function dropTable(string $tableName)
    {
        $table = $this->getMigrationsTable($tableName);
        $table->drop();
        $table->update();
        BcUtil::clearModelCache();
        $this->clearAppTableList();
        return true;
    }

    /**
     * PHP←→DBエンコーディングマップ
     *
     * @var array
     */
    protected $_encodingMaps = ['utf8' => 'UTF-8', 'sjis' => 'SJIS', 'ujis' => 'EUC-JP'];

    /**
     * 初期データを読み込む
     *
     * @param $theme
     * @param $pattern
     * @param $excludes
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadDefaultDataPattern($theme, $pattern): bool
    {
        $plugins = array_merge(
            ['BaserCore'],
            Configure::read('BcApp.corePlugins'),
            BcUtil::getCurrentThemesPlugins()
        );

        // データを削除する
        $excludes = ['plugins', 'dblogs', 'users'];
        $this->resetAllTables($excludes);

        $result = true;
        $this->clearAppTableList();

        foreach($plugins as $plugin) {
            if (!$this->_loadDefaultDataPattern($pattern, $theme, $plugin, $excludes)) {
                $result = false;
                $this->log(sprintf(__d('baser', '%s %s の初期データのロードに失敗しました。'), $theme . '.' . $pattern, $plugin));
            }
        }

        if (!$result) {
            $this->resetAllTables($excludes);
            // 指定したデータセットでの読み込みに失敗した場合、コアのデータ読み込みを試みる
            if ($theme !== Configure::read('BcApp.defaultFrontTheme')) {
                $theme = Configure::read('BcApp.defaultFrontTheme');
                foreach($plugins as $plugin) {
                    if (!$this->_loadDefaultDataPattern($pattern, $theme, $plugin, $excludes)) {
                        $result = false;
                        $this->log(sprintf(__d('baser', '%s %s の初期データのロードに失敗しました。'), $theme . '.' . $pattern, $plugin));
                    }
                }
            }
            if ($result) {
                throw new BcException(__d('baser', '初期データの読み込みに失敗しましたので baserCMSコアの初期データを読み込みました。ログを確認してください。'));
            } else {
                throw new BcException(__d('baser', '初期データの読み込みに失敗しました。データが不完全な状態です。正常に動作しない可能性があります。ログを確認してください。'));
            }
        }
        return $result;
    }

    /**
     * 初期データを読み込む
     *
     * @param string $pattern
     * @param string $theme
     * @param string $plugin
     * @param array $excludes
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _loadDefaultDataPattern($pattern, $theme, $plugin = 'BaserCore', $excludes = [])
    {
        $path = BcUtil::getDefaultDataPath($theme, $pattern);
        if (!$path) return true;

        $Folder = new Folder($path . DS . $plugin);
        $files = $Folder->read(true, true, true);
        $targetTables = $files[1];
        $tableList = $this->getAppTableList($plugin);
        $result = true;
        foreach($targetTables as $targetTable) {
            $targetTable = basename($targetTable, '.csv');
            if (in_array($targetTable, $excludes)) continue;
            if (!in_array($targetTable, $tableList)) continue;
            // 初期データ投入
            foreach($files[1] as $file) {
                if (!preg_match('/\.csv$/', $file)) continue;
                $table = basename($file, '.csv');
                if ($table !== $targetTable) continue;
                if (!$this->loadCsv(['path' => $file, 'encoding' => 'auto'])) {
                    $this->log(sprintf(__d('baser', '%s の読み込みに失敗。'), $file));
                    $result = false;
                } else {
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * CSVファイルをDBに読み込む
     *
     * @param array $options
     *  - `path`: 読み込み元のCSVのパス
     *  - `encoding: CSVファイルのエンコード
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadCsv($options): bool
    {
        $options = array_merge([
            'path' => null,
            'encoding' => $this->_dbEncToPhp($this->getEncoding())
        ], $options);

        if (!$options['path']) {
            return false;
        }

        $table = basename($options['path'], '.csv');
        $appTable = TableRegistry::getTableLocator()
            ->get('BaserCore.App');
        $schema = $appTable
            ->getConnection()
            ->getSchemaCollection()
            ->describe($table);
        $appTable->setTable($table);
        $appTable->setSchema($schema);
        $indexField = $schema->getPrimaryKey()[0];
        $records = $this->loadCsvToArray($options['path'], $options['encoding']);
        if ($records) {
            foreach($records as $record) {
                foreach($record as $key => $value) {
                    // 主キーでデータが空の場合はスキップ
                    if ($key === $indexField && empty($value)) {
                        unset($record[$indexField]);
                    }
                    if ($key === 'created' && empty($value)) {
                        $record['created'] = date('Y-m-d H:i:s');
                    } elseif ($schema->getColumnType($key) === 'datetime' && empty($value)) {
                        $record[$key] = null;
                    }
                    if ($schema->getColumnType($key) === 'boolean' && empty($value)) {
                        $record[$key] = 0;
                    }
                }
                try {
                    if (!$appTable->saveOrFail(new Entity($record))) {
                        return false;
                    }
                } catch (BcException $e) {
                    $this->log($e->getMessage());
                    return false;
                }

            }
        }

        return true;
    }

    /**
     * プラグインも含めて全てのテーブルをリセットする
     *
     * プラグインは有効となっているもののみ
     * 現在のテーマでないテーマの梱包プラグインを検出できない為
     *
     * @param array $dbConfig
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function resetAllTables($excludes = []): bool
    {
        $result = true;
        $this->clearAppTableList();
        $plugins = Plugin::loaded();
        foreach($plugins as $plugin) {
            if (!$this->resetTables($plugin, $excludes)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * 複数のテーブルをリセットする
     *
     * @param string $plugin
     * @param array $excludes
     * @return boolean
     * @noTodo
     * @checked
     * @unitTest
     */
    public function resetTables($plugin = 'BaserCore', $excludes = []): bool
    {
        $result = true;
        $tables = $this->getAppTableList($plugin);
        if (empty($tables)) return true;
        foreach($tables as $table) {
            if (!in_array($table, $excludes)) {
                if (!$this->truncate($table)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * テーブルのデータをリセットする
     *
     * @param $table
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function truncate($table): bool
    {
        $table = TableRegistry::getTableLocator()->get(
            Inflector::camelize($table),
            ['allowFallbackClass' => true]
        );
        $schema = $table->getSchema();
        $db = $table->getConnection();
        return (bool)$db->execute($schema->truncateSql($db)[0]);
    }

    /**
     * システムデータを初期化する
     *
     * @param string $dbConfigKeyName
     * @param array $dbConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initSystemData($options = []): bool
    {
        $options = array_merge([
            'excludeUsers' => false,
            'adminTheme' => '',
            'email' => null,
            'google_analytics_id' => null,
            'first_access' => true,
            'version' => null,
            'theme' => null,
        ], $options);

        $corePath = BcUtil::getPluginPath(Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-')) . 'config' . DS . 'data' . DS . 'default' . DS . 'BaserCore';
        $result = true;

        // user_groupsの初期データをチェック＆設定
        $userGroupTable = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
        if (!$userGroupTable->find()->where(['UserGroups.name' => 'admins'])->count()) {
            $userGroups = $this->loadCsvToArray($corePath . DS . 'user_groups.csv');
            foreach($userGroups as $userGroup) {
                if ($userGroup['name'] === 'admins') {
                    $userGroupTable->save(new Entity($userGroup));
                    break;
                }
            }
        }
        $usersUserGroupTable = TableRegistry::getTableLocator()->get('BaserCore.UsersUserGroups');
        $usersUserGroups = $this->loadCsvToArray($corePath . DS . 'users_user_groups.csv');
        foreach($usersUserGroups as $usersUserGroup) {
            $usersUserGroupTable->save(new Entity($usersUserGroup));
        }

        // users は全てのユーザーを削除
        //======================================================================
        // ユーザーグループを新しく読み込んだ場合にデータの整合性がとれない可能性がある為
        //======================================================================
        if (!$options['excludeUsers']) {
            if (!$this->truncate('users')) {
                $this->log(__d('baser', 'users テーブルの初期化に失敗。'));
                $result = false;
            }
        }

        // site_configs の初期データをチェック＆設定
        /* @var SiteConfigsService $siteConfigsService */
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->setValue('email', $options['email']);
        $siteConfigsService->setValue('google_analytics_id', $options['google_analytics_id']);
        $siteConfigsService->setValue('first_access', $options['first_access']);
        $siteConfigsService->setValue('admin_theme', !empty($options['adminTheme'])? Inflector::camelize(Inflector::underscore($options['adminTheme'])) : null);
        $siteConfigsService->setValue('version', $options['version']);

        // sites の初期データを設定
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sitesTable->get(1);
        $site->theme = $options['theme'];
        if (!$sitesTable->save($site)) {
            $result = false;
        }

        if (!$result) {
            $this->log(__d('baser', 'システムデータの初期化に失敗しました。'));
        }
        return $result;
    }

    /**
     * メールメッセージテーブルを初期化する
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initMessageTables(): bool
    {
        // TODO ucmitz メールプラグイン未実装のため
        return true;
        BcUtil::clearAllCache();
        // メール受信テーブルの作成
        $MailMessage = new MailMessage();
        $result = true;
        if (!$MailMessage->reconstructionAll()) {
            $this->log(__d('baser', 'メールプラグインのメール受信用テーブルの生成に失敗しました。'));
            $result = false;
        }
        BcUtil::clearAllCache();
        TableRegistry::getTableLocator()->clear();
        return $result;
    }

    /**
     * データベースシーケンスをアップデートする
     */
    public function updateSequence()
    {
        // TODO ucmitz 未実装
        return;

        $db = ConnectionManager::get('default');
        $tables = $db->getSchemaCollection()->listTables();
        $result = true;
        foreach($tables as $table) {
            $sql = 'select setval(\'' . $this->getSequence($table) . '\', (select max(id) from ' . $table . '));';
            if (!$db->execute($sql)) $result = false;
        }
        return $result;
    }

    /**
     * シーケンスを取得する
     */
    public function getSequence($table, $field = 'id')
    {
        // TODO ucmitz 未実装
        // CakePHP4系で存在しないので、CakePHP2系のPostgresクラスより持ってきた
        if (is_object($table)) {
            $table = $this->fullTableName($table, false, false);
        }
        if (!isset($this->_sequenceMap[$table])) {
            $this->describe($table);
        }
        if (isset($this->_sequenceMap[$table][$field])) {
            return $this->_sequenceMap[$table][$field];
        }
        return "{$table}_{$field}_seq";
    }

    /**
     * CSVよりデータを配列として読み込む
     *
     * @param string $path
     * @return false|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadCsvToArray($path, $encoding = 'auto')
    {

        if (!$encoding) {
            $encoding = $this->_dbEncToPhp($this->getEncoding());
        }
        if ($encoding === 'auto') {
            $encoding = mb_detect_encoding(file_get_contents($path));
        }
        $appEncoding = Configure::read('App.encoding');

        // ヘッダ取得
        $fp = fopen($path, 'r');
        if (!$fp) {
            return false;
        }

        $head = fgetcsv($fp, 10240);
        // UTF-8（BOM付）で何故か、配列の最初のキーに""が付加されてしまう
        $head[0] = preg_replace('/^﻿"(.+)"$/', "$1", $head[0]);

        $records = [];
        while(($record = BcUtil::fgetcsvReg($fp, 10240)) !== false) {
            if ($appEncoding != $encoding) {
                mb_convert_variables($appEncoding, $encoding, $record);
            }
            $values = [];
            foreach($record as $key => $value) {
                $values[$head[$key]] = $value;
            }
            $records[] = $values;
        }
        fclose($fp);

        return $records;

    }

    /**
     * DBのデータをCSVファイルとして書きだす
     *
     * @param array $options
     * -`path`: CSVの出力先となるパス
     * -`encoding`: 出力エンコーディング
     * -`table`: テーブル名
     * -`init`: id、created、modified を初期化する（初期値：false）
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function writeCsv($table, $options): bool
    {
        $options = array_merge([
            'path' => '',
            'encoding' => '',
            'init' => false,
        ], $options);

        if (empty($options['path'])) {
            return false;
        }
        if (empty($options['encoding'])) {
            $options['encoding'] = Configure::read('App.encoding');
        }
        $db = TableRegistry::getTableLocator()
            ->get('BaserCore.App')
            ->getConnection();
        $schema = $db->getSchemaCollection()->describe(
            $table,
            ['forceRefresh' => true]
        );

        $appEncoding = $this->_dbEncToPhp($this->getEncoding());
        $sql = 'SELECT `' . implode('`,`', $schema->columns()) . '` FROM ' . $table;
        $query = $db->query($sql);
        $records = $query->fetchAll('assoc');

        $fp = fopen($options['path'], 'w');
        ftruncate($fp, 0);

        // ヘッダを書込
        if ($records) {
            $heads = [];
            foreach($records[0] as $key => $value) {
                $heads[] = '"' . $key . '"';
            }
        } else {
            foreach($schema->columns() as $field) {
                $heads[] = '"' . $field . '"';
            }
        }
        if ($options['encoding'] == 'UTF-8') {
            fwrite($fp, pack('C*', 0xEF, 0xBB, 0xBF));
        }
        $head = implode(",", $heads) . "\n";
        if ($options['encoding'] !== $appEncoding) {
            $head = mb_convert_encoding($head, $options['encoding'], $appEncoding);
        }
        fwrite($fp, $head);

        // データを書込
        foreach($records as $record) {
            if ($options['init']) {
                $record['id'] = '';
                $record['modified'] = '';
                $record['created'] = '';
            }
            $record = $this->_convertRecordToCsv($record);
            $csvRecord = implode(',', $record) . "\n";
            if ($options['encoding'] !== $appEncoding) {
                $csvRecord = mb_convert_encoding($csvRecord, $options['encoding'], $appEncoding);
            }
            fwrite($fp, $csvRecord);
        }

        fclose($fp);
        return true;
    }

    /**
     * CSV用のレコードデータに変換する
     *
     * @param array $record
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _convertRecordToCsv($record)
    {
        foreach($record as $field => $value) {
            $record[$field] = $this->_convertFieldToCsv($value);
        }
        return $record;
    }

    /**
     * CSV用のフィールドデータに変換する
     *
     * @param string $value
     * @param boolean $dc （ " を "" に変換するか）
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _convertFieldToCsv($value, $dc = true)
    {
        if (!$value) return '';
        if ($dc) $value = str_replace('"', '""', $value);
        $value = trim(trim($value), "\'");
        $value = str_replace("\\'", "'", $value);
        $value = str_replace('{CM}', ',', $value);
        $value = '"' . $value . '"';
        return $value;
    }

    /**
     * DB用エンコーディング名称をPHP用エンコーディング名称に変換する
     *
     * @param string $enc
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _dbEncToPhp($enc)
    {
        if (is_array($enc)) {
            if (!empty($enc)) {
                if (is_array($enc[0])) {
                    $enc = $enc[0][0];
                } else {
                    $enc = $enc[0];
                }
            } else {
                $enc = '';
            }
        }
        if (!empty($this->_encodingMaps[$enc])) {
            return $this->_encodingMaps[$enc];
        } else {
            return $enc;
        }
    }

    /**
     * PHP用エンコーディング名称をDB用のエンコーディング名称に変換する
     *
     * @param string $enc
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _phpEncToDb($enc)
    {
        $encs = array_keys($this->_encodingMaps, $enc);
        if ($encs && is_array($encs)) {
            return $encs[0];
        } else {
            return $enc;
        }
    }

    /**
     * Gets the database encoding
     *
     * @return string The database encoding
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEncoding(): string
    {
        return 'utf8';
    }

    /**
     * アプリケーションに関連するテーブルリストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAppTableList($plugin = ''): array
    {
        $list = Cache::read('appTableList', '_bc_env_');
        if ($list) {
            if ($plugin) {
                return (isset($list[$plugin]))? $list[$plugin] : [];
            } else {
                return $list;
            }
        }
        $tables = TableRegistry::getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        $plugins = Plugin::loaded();
        $list = [];
        foreach($plugins as $value) {
            $pluginPath = BcUtil::getPluginPath($value);
            if (!$pluginPath) continue;
            $path = $pluginPath . 'config' . DS . 'Migrations';
            if (!is_dir($path)) continue;
            $folder = new Folder($path);
            $files = $folder->read(true, true);
            if (empty($files[1])) continue;
            foreach($files[1] as $file) {
                if (!preg_match('/Create([a-zA-Z]+)\./', $file, $matches)) continue;
                $checkName = Inflector::tableize($matches[1]);
                if (in_array($checkName, $tables)) {
                    $list[$value][] = $checkName;
                }
            }
        }
        Cache::write('appTableList', $list, '_bc_env_');
        if ($plugin) {
            return (isset($list[$plugin]))? $list[$plugin] : [];
        } else {
            return $list;
        }
    }

    /**
     * アプリケーションに関連するテーブルリストのキャッシュをクリアする
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearAppTableList(): void
    {
        Cache::write('appTableList', [], '_bc_env_');
    }

    /**
     * モデル名を指定してスキーマファイルを生成する
     *
     * @param Table テーブルオブジェクト名
     * @param array $options
     *  - `path` : スキーマファイルの生成場所
     * @return string|false スキーマファイルの内容
     * @unitTest
     * @noTodo
     * @unitTest
     */
    public function writeSchema($table, $options)
    {
        $dir = dirname($options['path']);
        if (!is_dir($dir)) {
            $folder = new Folder();
            $folder->create($dir);
        }
        $describe = TableRegistry::getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->describe($table);
        $schema = $this->_generateSchema($describe);
        $renderer = new View();
        $renderer->disableAutoLayout();
        $renderer->set([
            'name' => Inflector::camelize($table),
            'table' => $table,
            'schema' => $schema
        ]);

        $eventManager = EventManager::instance();
        $beforeRenderListeners = BcUtil::offEvent($eventManager, 'View.beforeRender');
        $afterRenderListeners = BcUtil::offEvent($eventManager, 'View.afterRender');

        $content = "<?php\n\n" . $renderer->render('BaserCore.BcDatabaseService/schema');

        BcUtil::onEvent($eventManager, 'View.beforeRender', $beforeRenderListeners);
        BcUtil::onEvent($eventManager, 'View.afterRender', $afterRenderListeners);

        if (!is_dir($options['path'])) {
            $folder = new Folder();
            $folder->create($options['path']);
        }

        $file = new File($options['path'] . DS . Inflector::camelize($table) . 'Schema.php');
        $file->write($content);
        $file->close();
        return true;
    }

    /**
     * テキストのスキーマ情報を生成する
     * Bake\Command\FixtureCommand::_generateSchema() を移植
     *
     * @param TableSchemaInterface $table
     * @return string
     * @checked
     * @noTodo
     * @unitTest 移植のためスキップ
     */
    protected function _generateSchema(TableSchemaInterface $table)
    {
        $cols = $indexes = $constraints = [];
        foreach($table->columns() as $field) {
            $fieldData = $table->getColumn($field);
            $properties = implode(', ', $this->_values($fieldData));
            $cols[] = "        '$field' => [$properties],";
        }
        foreach($table->indexes() as $index) {
            $fieldData = $table->getIndex($index);
            $properties = implode(', ', $this->_values($fieldData));
            $indexes[] = "            '$index' => [$properties],";
        }
        foreach($table->constraints() as $index) {
            $fieldData = $table->getConstraint($index);
            $properties = implode(', ', $this->_values($fieldData));
            $constraints[] = "            '$index' => [$properties],";
        }
        $options = $this->_values($table->getOptions());

        $content = implode("\n", $cols) . "\n";
        if (!empty($indexes)) {
            $content .= "        '_indexes' => [\n" . implode("\n", $indexes) . "\n        ],\n";
        }
        if (!empty($constraints)) {
            $content .= "        '_constraints' => [\n" . implode("\n", $constraints) . "\n        ],\n";
        }
        if (!empty($options)) {
            foreach($options as &$option) {
                $option = '            ' . $option;
            }
            $content .= "        '_options' => [\n" . implode(",\n", $options) . "\n        ],\n";
        }

        return "[\n$content    ]";
    }

    /**
     * Formats Schema columns from Model Object
     * Bake\Command\FixtureCommand::_values() を移植
     *
     * @param array $values options keys(type, null, default, key, length, extra)
     * @return string[] Formatted values
     * @checked
     * @noTodo
     * @unitTest 移植のためスキップ
     */
    protected function _values(array $values): array
    {
        $vals = [];

        foreach($values as $key => $val) {
            if (is_array($val)) {
                $vals[] = "'{$key}' => [" . implode(', ', $this->_values($val)) . ']';
            } else {
                $val = var_export($val, true);
                if ($val === 'NULL') {
                    $val = 'null';
                }
                if (!is_numeric($key)) {
                    $vals[] = "'{$key}' => {$val}";
                } else {
                    $vals[] = "{$val}";
                }
            }
        }

        return $vals;
    }

    /**
     * スキーマを読み込む
     *
     * @param $options
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadSchema($options): bool
    {
        $options = array_merge([
            'type' => 'create',
            'path' => '',
            'file' => ''
        ], $options);
        $schemaName = basename($options['file'], '.php');
        require_once $options['path'] . $options['file'];
        /* @var BcSchema $schema */
        $schema = new $schemaName();
        switch($options['type']) {
            case 'create':
                $schema->create();
                break;
            case 'drop':
                $schema->drop();
                break;
        }
        return true;
    }

    /**
     * datasource名を取得
     *
     * @param string datasource name.postgre.mysql.etc.
     * @return string
     */
    public function getDatasourceName($datasource = null)
    {
        $name = $datasource;
        switch($datasource) {
            case 'postgres' :
                $name = Postgres::class;
                break;
            case 'mysql' :
                $name = Mysql::class;
                break;
            case 'sqlite' :
                $name = Sqlite::class;
                break;
            default :
        }
        return $name;
    }

    /**
     * データベースに接続する
     *
     * @param array $config
     */
    public function connectDb(array $config, $name = 'default')
    {
        if (empty($config['driver']) && !empty($config['datasource'])) {
            $config['driver'] = $this->getDatasourceName($config['datasource']);
        }
        if (empty($config['driver'])) return ConnectionManager::get($name);
        ConnectionManager::setConfig($name, [
            'className' => Connection::class,
            'driver' => $config['driver'],
            'persistent' => false,
            'host' => $config['host'],
            'port' => $config['port'],
            'username' => $config['username'],
            'password' => $config['password'],
            'database' => $config['database'],
            'schema' => $config['schema'],
            'prefix' => $config['prefix'],
            'encoding' => $config['encoding']
        ]);
        $db = ConnectionManager::get($name);
        $db->connect();
        return $db;
    }

    /**
     * データソースを取得する
     *
     * @param string $configKeyName
     * @param array $dbConfig
     * @return Connection
     */
    public function getDataSource($dbConfigKeyName = 'default', $dbConfig = null)
    {
        try {
            $db = ConnectionManager::get($dbConfigKeyName);
        } catch (MissingDatasourceConfigException $e) {
            if ($dbConfig) {
                if (empty($dbConfig['driver']) && !empty($dbConfig['datasource'])) {
                    $dbConfig['driver'] = $this->getDatasourceName($dbConfig['datasource']);
                }
                ConnectionManager::setConfig($dbConfigKeyName, $dbConfig);
                $db = ConnectionManager::get($dbConfigKeyName);
            } else {
                throw $e;
            }
        }
        return $db;
    }

    /**
     * テーブルを削除する
     *
     * @param string $dbConfigKeyName
     * @param array $dbConfig
     * @return boolean
     */
    public function deleteTables($dbConfigKeyName = 'default', $dbConfig = null)
    {
        $db = $this->getDataSource($dbConfigKeyName, $dbConfig);
        if (!$dbConfig) $dbConfig = ConnectionManager::getConfig($dbConfigKeyName);
        $prefix = Configure::read('Datasources.default.prefix');
        $datasource = strtolower(str_replace('Cake\\Database\\Driver\\', '', $dbConfig['driver']));
        switch($datasource) {
            case 'mysql':
                $sources = $db->getSchemaCollection()->listTables();
                foreach($sources as $source) {
                    if (!preg_match("/^" . $prefix . "([^_].+)$/", $source)) continue;
                    try {
                        $db->execute('DROP TABLE ' . $source);
                    } catch (BcException $e) {
                    }
                }
                break;

            case 'postgres':
                $sources = $db->getSchemaCollection()->listTables();
                foreach($sources as $source) {
                    if (!preg_match("/^" . $prefix . "([^_].+)$/", $source)) continue;
                    try {
                        $db->execute('DROP TABLE ' . $source);
                    } catch (BcException $e) {
                    }
                }
                // シーケンスも削除
                $sql = "SELECT sequence_name FROM INFORMATION_SCHEMA.sequences WHERE sequence_schema = '{$dbConfig['schema']}';";
                $sequences = [];
                try {
                    $sequences = $db->query($sql);
                } catch (BcException $e) {
                }
                if ($sequences) {
                    $sequences = Hash::extract($sequences, '0.sequence_name');
                    foreach($sequences as $sequence) {
                        if (!preg_match("/^" . $prefix . "([^_].+)$/", $sequence)) continue;
                        $sql = 'DROP SEQUENCE ' . $sequence;
                        try {
                            $db->execute($sql);
                        } catch (BcException $e) {
                        }
                    }
                }
                break;

            case 'sqlite':
                @unlink($dbConfig['database']);
                break;
        }
        return true;
    }

    /**
     * DB接続チェック
     *
     * @param string[] $config
     *  - `datasource`: 'MySQL' or 'Postgres' or 'SQLite' or 'CSV'
     *  - `database`: データベース名 SQLiteの場合はファイルパス CSVの場合はディレクトリへのパス
     *  - `host`: テキストDB or localhostの場合は不要
     *  - `port`: 接続ポート テキストDBの場合は不要
     *  - `login`: 接続ユーザ名 テキストDBの場合は不要
     *  - `password`: 接続パスワード テキストDBの場合は不要
     * @throws PDOException
     * @throws BcException
     */
    public function checkDbConnection($config)
    {
        $datasource = Hash::get($config, 'datasource');
        $database = Hash::get($config, 'database');
        $host = Hash::get($config, 'host');
        $port = Hash::get($config, 'port');
        $login = Hash::get($config, 'username');
        $password = Hash::get($config, 'password');
        $datasource = strtolower($datasource);

        try {
            switch($datasource) {
                case 'mysql':
                    $dsn = "mysql:dbname={$database}";
                    if ($host) $dsn .= ";host={$host}";
                    if ($port) $dsn .= ";port={$port}";
                    $pdo = new PDO($dsn, $login, $password);
                    break;
                case 'postgres':
                    $dsn = "pgsql:dbname={$database}";
                    if ($host) $dsn .= ";host={$host}";
                    if ($port) $dsn .= ";port={$port}";
                    $pdo = new PDO($dsn, $login, $password);
                    break;
                case 'sqlite':
                    if (file_exists($database)) {
                        if (!is_writable($database)) {
                            throw new BcException(__d('baser', "データベースファイルに書き込み権限がありません。"));
                        }
                    } else {
                        if (!is_writable(dirname($database))) {
                            throw new BcException(__d('baser', 'データベースの保存フォルダに書き込み権限がありません。'));
                        }
                    }
                    $dsn = "sqlite:" . $database;
                    $pdo = new PDO($dsn);
                    break;
                default:
                    throw new BcException(__d('baser', 'ドライバが見つかりません Driver is not defined.(MySQL|Postgres|SQLite)'));
            }
            unset($pdo);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * データベース接続テスト
     *
     * @param array $config
     */
    public function testConnectDb($config)
    {
        /* データベース接続確認 */
        try {
            $this->checkDbConnection($config);
        } catch (PDOException $e) {
            if ($e->getCode() === 2002) {
                throw new PDOException(__d('baser', "データベースへの接続でエラーが発生しました。データベース設定を見直してください。\nサーバー上に指定されたデータベースが存在しない可能性が高いです。\n" . $e->getMessage()));
            }
            throw $e;
        } catch (BcException $e) {
            $message = __d('baser', "データベースへの接続でエラーが発生しました。データベース設定を見直してください。\nサーバー上に指定されたデータベースが存在しない可能性が高いです。");
            if (preg_match('/with message \'(.+?)\' in/s', $e->getMessage(), $matches)) {
                $message .= "\n" . $matches[1];
            }
            throw new BcException($message);
        }

        /* データベース接続生成 */
        /* @var Connection $db */
        $db = $this->connectDb($config);

        if (!$db->isConnected()) {
            throw new BcException(__d('baser', "データベースへの接続でエラーが発生しました。データベース設定を見直してください。"));
        }

        //version check
        $driver = $db->getDriver();
        switch(get_class($driver)) {
            case 'Cake\Database\Driver\Mysql' :
                $result = $db->execute("SELECT version() as version")->fetch();
                if (version_compare($result[0], Configure::read('BcRequire.MySQLVersion')) == -1) {
                    throw new BcException(sprintf(__d('baser', 'データベースのバージョンが %s 以上か確認してください。'), Configure::read('BcRequire.MySQLVersion')));
                }
                break;
            case 'Cake\Database\Driver\Postgres' :
                // TODO ucmitz 未検証
                $result = $db->query("SELECT version() as version")->fetch();
                [, $version] = explode(" ", $result[0]);
                if (version_compare(trim($version), Configure::read('BcRequire.PostgreSQLVersion')) == -1) {
                    throw new BcException(sprintf(__d('baser', 'データベースのバージョンが %s 以上か確認してください。'), Configure::read('BcRequire.PostgreSQLVersion')));
                }
                break;
        }

        try {
            /* 一時的にテーブルを作成できるかテスト */
            $randomtablename = 'deleteme' . rand(100, 100000);
            $db->execute("CREATE TABLE $randomtablename (a varchar(10))");
            $db->execute('drop TABLE ' . $randomtablename);
        } catch (PDOException $e) {
            throw new PDOException(__d('baser', "データベースへの接続でエラーが発生しました。\n") . $e->getMessage());
        }

        // データベースのテーブルをチェック
        $tableNames = $db->getSchemaCollection()->listTables();
        $prefix = Hash::get($config, 'prefix');
        $duplicateTableNames = array_filter($tableNames, function($tableName) use ($prefix) {
            return strpos($tableName, $prefix) === 0;
        });
        if (count($duplicateTableNames) > 0) {
            throw new BcException(__d('baser', 'データベースへの接続に成功しましたが、プレフィックスが重複するテーブルが存在します。') . implode(', ', $duplicateTableNames));
        }
    }

    /**
     * テーブルを構築する
     *
     * @param string $plugin
     * @param string $dbConfigKeyName
     * @param array $dbConfig
     * @return boolean
     */
    public function constructionTable(string $plugin, string $dbConfigKeyName = 'default', array $dbConfig = [])
    {
        $db = $this->getDataSource($dbConfigKeyName, $dbConfig);
        if (!$dbConfig) $dbConfig = ConnectionManager::getConfig($dbConfigKeyName);
        $datasource = strtolower(str_replace('Cake\\Database\\Driver\\', '', $dbConfig['driver']));
        if (!$db->isConnected()) {
            return false;
        } elseif ($datasource == 'sqlite') {
            $db->connect();
            chmod($dbConfig['database'], 0666);
        }
        return $this->migrate($plugin, $dbConfigKeyName);
    }

    /**
     * データベースのマイグレーションを実行する
     *
     * @param string $plugin
     * @param string $connection
     * @return bool
     */
    public function migrate(string $plugin, string $connection = 'default')
    {
        $pluginPath = BcUtil::getPluginPath($plugin);
        if (!is_dir($pluginPath . 'config' . DS . 'Migrations')) return false;
        $options = [
            'plugin' => $plugin,
            'connection' => $connection
        ];
        try {
            $migrations = new Migrations();
            $migrations->migrate([
                'plugin' => $plugin,
                'connection' => $connection
            ]);
            return true;
        } catch (BcException $e) {
            $this->log($e->getMessage());
            $migrations->rollback($options);
            return false;
        }
    }

}
