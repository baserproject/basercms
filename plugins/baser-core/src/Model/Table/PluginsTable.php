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

namespace BaserCore\Model\Table;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginsTable
 * @package BaserCore\Model\Table
 */
class PluginsTable extends Table
{

    /**
     * Plugin constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     * @checked
     */
    public function __construct($config)
    {
        parent::__construct($config);
        // TODO 暫定措置
        // >>>
        return;
        // <<<
        $this->validate = [
            'name' => [
                ['rule' => ['alphaNumericPlus'], 'message' => __d('baser', 'プラグイン名は半角英数字、ハイフン、アンダースコアのみが利用可能です。'), 'required' => true],
                ['rule' => ['isUnique'], 'on' => 'create', 'message' => __d('baser', '指定のプラグインは既に使用されています。')],
                ['rule' => ['maxLength', 50], 'message' => __d('baser', 'プラグイン名は50文字以内としてください。')]],
            'title' => [
                ['rule' => ['maxLength', 50], 'message' => __d('baser', 'プラグインタイトルは50文字以内とします。')]]
        ];
    }

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

    /**
     * 利用可能なプラグインの一覧を取得
     *
     * @return array
     */
    public function getAvailable()
    {
        // プラグインフォルダーのチェックを行う
        $pluginConfigs = [];
        $paths = App::path('plugins');
        foreach($paths as $path) {
            $Folder = new Folder($path);
            $files = $Folder->read(true, true, true);
            foreach($files[0] as $file) {
                if (!in_array(basename($file), Configure::read('BcApp.core'))) {
                    $pluginConfigs[basename($file)] = $this->getPluginConfig(basename($file));
                }
            }
        }
        return array_reverse(array_values($pluginConfigs));
    }

    /**
     * プラグイン情報を取得する
     *
     * @param string $name プラグイン名
     * @return \BaserCore\Model\Entity\Plugin|\Cake\Datasource\EntityInterface
     */
    public function getPluginConfig($name)
    {

        $pluginName = Inflector::camelize($name, '-');

        // プラグインのバージョンを取得
        $corePlugins = Configure::read('BcApp.corePlugins');
        if (in_array($pluginName, $corePlugins)) {
            $core = true;
            $version = BcUtil::getVersion();
        } else {
            $core = false;
            $version = BcUtil::getVersion($pluginName);
        }

        $result = $this->find()
            ->order(['priority'])
            ->where(['name' => $pluginName])
            ->first();

        if ($result) {
            $pluginRecord = $result;
            $this->patchEntity($pluginRecord, [
                'update' => false,
                'core' => $core,
                'permission' => 1,
                'registered' => true
            ]);
            if (BcUtil::verpoint($pluginRecord->version) < BcUtil::verpoint($version) &&
                !in_array($pluginRecord->name, Configure::read('BcApp.corePlugins'))
            ) {
                $pluginRecord->update = true;
            }
        } else {
            $pluginRecord = $this->newEntity([
                'id' => '',
                'name' => $pluginName,
                'created' => '',
                'version' => $version,
                'status' => false,
                'update' => false,
                'core' => $core,
                'permission' => 1,
                'registered' => false,
            ]);
        }

        // 設定ファイル読み込み
        $appConfigPath = BcUtil::getPluginPath($name) . 'config.php';
        if (file_exists($appConfigPath)) {
            $this->patchEntity($pluginRecord, include $appConfigPath);
        }
        return $pluginRecord;
    }

    /**
     * インストール可能かチェックする
     *
     * @param $pluginName
     * @return bool
     */
    public function isInstallable($pluginName)
    {
        $installedPlugin = $this->find()->where([
            'name' => $pluginName,
            'status' => true,
        ])->first();

        // 既にプラグインがインストール済み
        if ($installedPlugin) {
            throw new BcException('既にインストール済のプラグインです。');
        }

        $paths = App::path('plugins');
        $existsPluginFolder = false;
        $folder = $pluginName;
        foreach($paths as $path) {
            if (!is_dir($path . $folder)) {
                $dasherize = Inflector::dasherize($folder);
                if (!is_dir($path . $dasherize)) {
                    continue;
                }
                $folder = $dasherize;
            }
            $existsPluginFolder = true;
            $configPath = $path . $folder . DS . 'config.php';
            if (file_exists($configPath)) {
                $config = include $configPath;
            }
            break;
        }

        // プラグインのフォルダが存在しない
        if (!$existsPluginFolder) {
            throw new BcException('インストールしようとしているプラグインのフォルダが存在しません。');
        }

        // インストールしようとしているプラグイン名と、設定ファイル内のプラグイン名が違う
        if (!empty($config['name']) && $pluginName !== $config['name']) {
            throw new BcException('このプラグイン名のフォルダ名を' . $config['name'] . 'にしてください。');
        }

        return true;
    }

    public function isDbInit($name) {

    }

    /**
     * プラグインをインストールする
     *
     * @param $name
     * @return bool
     */
    public function install($name): bool
    {
        $recordExists = $this->find()->where(['name' => $name])->count();
        $plugin = $this->getPluginConfig($name);
        if (!$recordExists) {
			$corePlugins = Configure::read('BcApp.corePlugins');
			if (in_array($name, $corePlugins)) {
				$version = BcUtil::getVersion();
			} else {
				$version = BcUtil::getVersion($name);
			}
            $query = $this->find();
            $priority = $query->select(['max' => $query->func()->max('priority')])->first();
			$plugin->version = ($version)? $version : null;
            $plugin->priority = $priority->max + 1;
            $plugin->db_init = true;
            $plugin->status = true;
        } else {
            $plugin->db_init = true;
            $plugin->status = true;
        }
        if ($this->save($plugin)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * プラグインをアンインストールする
     *
     * @param $name
     * @return bool
     */
    public function uninstall($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        return $this->delete($targetPlugin);
    }

    /**
     * プラグインを無効化する
     *
     * @param $name
     * @return bool
     * @checked
     */
    public function detach($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        if ($targetPlugin === null) {
            return false;
        }
        $targetPlugin->status = false;
        $result = $this->save($targetPlugin);
        // TODO 未実装
        /* >>>
        clearAllCache();
        <<< */
        return $result !== false;
    }

    /**
     * データベースを初期化する
     * 既存のテーブルは上書きしない
     *
     * @param string $dbConfigName データベース設定名
     * @param string $pluginName プラグイン名
     * @param bool $loadCsv CSVファイル読込するかどうか
     * @param string $filterTable テーブル指定
     * @param string $filterType 更新タイプ指定
     * @return bool
     */
    public function initDb($pluginName = '', $options = [])
    {
        if (!is_array($options)) {
            // @deprecated 5.0.0 since 4.0.0 baserCMS３まで第二引数がプラグイン名だったが、第一引数にプラグイン名を設定するように変更。元の第一引数は不要
            $this->log(__d('baser', 'メソッド：Plugin::initDb()は、バージョン 4.0.0 より引数が変更になりました。第一引数にプラグイン名を設定してください。元の第一引数は不要です。'), LOG_ALERT);
            $pluginName = $options;
            $options = [];
        }
        $options = array_merge([
            'loadCsv' => true,
            'filterTable' => '',
            'filterType' => 'create',
            'dbDataPattern' => ''
        ], $options);
        return parent::initDb($pluginName, [
            'loadCsv' => $options['loadCsv'],
            'filterTable' => $options['filterTable'],
            'filterType' => $options['filterType'],
            'dbDataPattern' => $options['dbDataPattern']
        ]);
    }

    /**
     * データベースをプラグインインストール前の状態に戻す
     *
     * @param string $plugin プラグイン名
     * @return bool
     */
    public function resetDb($plugin)
    {
        $path = BcUtil::getSchemaPath($plugin);

        if (!$path) {
            return true;
        }

        $db = ConnectionManager::getDataSource('default');
        $db->cacheSources = false;
        $listSources = $db->listSources();
        $prefix = $db->config['prefix'];

        $Folder = new Folder($path);
        $files = $Folder->read(true, true);

        if (empty($files[1])) {
            return true;
        }

        $tmpdir = TMP . 'schemas' . DS;
        $result = true;

        foreach($files[1] as $file) {

            $oldSchemaPath = '';

            if (preg_match('/^(.*?)\.php$/', $file, $matches)) {

                $type = 'drop';
                $table = $matches[1];

                $schemaPath = $tmpdir;
                if (preg_match('/^create_(.*?)\.php$/', $file, $matches)) {
                    $type = 'drop';
                    $table = $matches[1];
                    if (!in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                    copy($path . DS . $file, $tmpdir . $table . '.php');
                } elseif (preg_match('/^alter_(.*?)\.php$/', $file, $matches)) {
                    $type = 'alter';
                    $table = $matches[1];
                    if (!in_array($prefix . $table, $listSources)) {
                        continue;
                    }

                    $corePlugins = implode('|', Configure::read('BcApp.corePlugins'));
                    if (preg_match('/^(' . $corePlugins . ')/', Inflector::camelize($table), $matches)) {
                        $pluginName = $matches[1];
                    }

                    $File = new File($path . DS . $file);
                    $data = $File->read();
                    $data = preg_replace('/class\s+' . Inflector::camelize($table) . 'Schema/', 'class Alter' . Inflector::camelize($table) . 'Schema', $data);
                    $oldSchemaPath = $tmpdir . $file;
                    $File = new File($oldSchemaPath);
                    $File->write($data);
                    $schemaPath = BcUtil::getSchemaPath($pluginName) . DS;
                } elseif (preg_match('/^drop_(.*?)\.php$/', $file, $matches)) {
                    $type = 'create';
                    $table = $matches[1];
                    if (in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                    copy($path . DS . $file, $tmpdir . $table . '.php');
                } else {
                    if (!in_array($prefix . $table, $listSources)) {
                        continue;
                    }
                    copy($path . DS . $file, $tmpdir . $table . '.php');
                }

                if (!$db->loadSchema(['type' => $type, 'path' => $schemaPath, 'file' => $table . '.php', 'dropField' => true, 'oldSchemaPath' => $oldSchemaPath])) {
                    $result = false;
                }
                @unlink($tmpdir . $table . '.php');
                if (file_exists($oldSchemaPath)) {
                    unlink($oldSchemaPath);
                }
            }
        }

        return $result;
    }

    /**
     * データベースの構造を変更する
     *
     * @param string $plugin プラグイン名
     * @param string $dbConfigName データベース設定名
     * @param string $filterTable テーブル指定
     * @return bool
     * @deprecated 5.0.0 since 4.0.0 Plugin::initDb() に統合
     */
    public function alterDb($plugin, $options = [])
    {
        $this->log(__d('baser', 'メソッド：Plugin::alterDb()は、バージョン 4.0.0 より非推奨となりました。Plugin::initDb() を利用してください。'), LOG_ALERT);
        if (!is_array($options)) {
            $pluginName = $options;
            $options = [];
        }
        $options = array_merge([
            'filterTable' => '',
        ], $options);
        return $this->initDb($plugin, $options);
    }

    /**
     * 指定したフィールドに重複値があるかチェック
     *
     * @param string $fieldName チェックするフィールド名
     * @return bool
     */
    public function hasDuplicateValue($fieldName)
    {
        $this->cacheQueries = false;

        $duplication = $this->find('all', [
            'fields' => [
                "{$this->alias}.{$fieldName}"
            ],
            'group' => [
                "{$this->alias}.{$fieldName} HAVING COUNT({$this->alias}.id) > 1"
            ]
        ]);

        return !empty($duplication);
    }

    /**
     * 優先順位を連番で振り直す
     *
     * @return bool
     */
    public function rearrangePriorities()
    {
        $this->cacheQueries = false;
        $datas = $this->find('all', [
            'order' => 'Plugin.priority'
        ]);

        $count = count($datas);
        for($i = 0; $i < $count; $i++) {
            $datas[$i]['Plugin']['priority'] = $i + 1;
        }

        if (!$this->saveMany($datas)) {
            return false;
        }
        return true;
    }

    /**
     * 優先順位を変更する
     *
     * @param string|int $id 起点となるプラグインのID
     * @param string|int $offset 変更する範囲の相対位置
     * @param array $conditions find条件
     * @return bool
     */
    public function changePriority($id, $offset, $conditions = [])
    {
        $offset = intval($offset);
        if ($offset === 0) {
            return true;
        }

        $field = 'priority';
        $alias = $this->alias;

        // 一時的にキャッシュをOFFする
        $this->cacheQueries = false;

        $current = $this->findById($id, ["{$alias}.id", "{$alias}.{$field}"]);

        // currentを含め変更するデータを取得
        if ($offset > 0) { // DOWN
            $order = ["{$alias}.{$field}"];
            $conditions["{$alias}.{$field} >="] = $current[$alias][$field];
        } else { // UP
            $order = ["{$alias}.{$field} DESC"];
            $conditions["{$alias}.{$field} <="] = $current[$alias][$field];
        }

        $datas = $this->find('all', [
            'conditions' => $conditions,
            'fields' => ["{$alias}.id", "{$alias}.{$field}", "{$alias}.name"],
            'order' => $order,
            'limit' => abs($offset) + 1,
            'recursive' => -1
        ]);

        if (empty($datas)) {
            return false;
        }

        //データをローテーション
        $count = count($datas);
        $currentNewValue = $datas[$count - 1][$alias][$field];
        for($i = $count - 1; $i > 0; $i--) {
            $datas[$i][$alias][$field] = $datas[$i - 1][$alias][$field];
        }
        $datas[0][$alias][$field] = $currentNewValue;

        if (!$this->saveMany($datas)) {
            return false;
        };

        return true;
    }

    /**
     * プラグインのディレクトリパスを取得
     *
     * @param string $pluginName プラグイン名
     * @return string|null
     */
    public function getDirectoryPath($pluginName)
    {
        $paths = App::path('Plugin');
        foreach($paths as $path) {
            $Folder = new Folder($path);
            $files = $Folder->read(true, true, true);
            foreach($files[0] as $dir) {
                if (basename($dir) === $pluginName) {
                    return $dir;
                }
            };
        }
        return null;
    }

    /**
     * プラグイン情報を取得する
     *
     * @param array $datas プラグインのデータ配列
     * @param string $file プラグインファイルのパス
     * @return array
     */
    public function getPluginInfo($datas, $file)
    {
        $plugin = basename($file);
        $pluginData = [];
        $exists = false;
        foreach($datas as $data) {
            if ($plugin == $data['Plugin']['name']) {
                $pluginData = $data;
                $exists = true;
                break;
            }
        }

        // プラグインのバージョンを取得
        $corePlugins = Configure::read('BcApp.corePlugins');
        $core = false;
        if (in_array($plugin, $corePlugins)) {
            $core = true;
            $version = getVersion();
        } else {
            $version = getVersion($plugin);
        }

        // 設定ファイル読み込み
        $title = $description = $author = $url = $adminLink = '';

        // TODO 互換性のため古いパスも対応
        $oldAppConfigPath = $file . DS . 'Config' . DS . 'config.php';
        $appConfigPath = $file . DS . 'config.php';
        if (!file_exists($appConfigPath)) {
            $appConfigPath = $oldAppConfigPath;
        }

        if (file_exists($appConfigPath)) {
            include $appConfigPath;
        } elseif (file_exists($oldAppConfigPath)) {
            include $oldAppConfigPath;
        }

        if (isset($title)) {
            $pluginData['Plugin']['title'] = $title;
        }
        if (isset($description)) {
            $pluginData['Plugin']['description'] = $description;
        }
        if (isset($author)) {
            $pluginData['Plugin']['author'] = $author;
        }
        if (isset($url)) {
            $pluginData['Plugin']['url'] = $url;
        }

        $pluginData['Plugin']['update'] = false;
        $pluginData['Plugin']['old_version'] = false;
        $pluginData['Plugin']['core'] = $core;

        if ($exists) {

            if (isset($adminLink)) {
                $pluginData['Plugin']['admin_link'] = $adminLink;
            }
            // バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
            if (!$pluginData['Plugin']['version'] && preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version, $matches)) {
                $pluginData['Plugin']['version'] = $matches[1];
                $pluginData['Plugin']['old_version'] = true;
            } elseif (verpoint($pluginData['Plugin']['version']) < verpoint($version) && !in_array($pluginData['Plugin']['name'], Configure::read('BcApp.corePlugins'))) {
                $pluginData['Plugin']['update'] = true;
            }
            $pluginData['Plugin']['registered'] = true;
        } else {
            // バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
            if (preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version, $matches)) {
                $version = $matches[1];
                $pluginData['Plugin']['old_version'] = true;
            }
            $pluginData['Plugin']['id'] = '';
            $pluginData['Plugin']['name'] = $plugin;
            $pluginData['Plugin']['created'] = '';
            $pluginData['Plugin']['version'] = $version;
            $pluginData['Plugin']['status'] = false;
            $pluginData['Plugin']['modified'] = '';
            $pluginData['Plugin']['admin_link'] = '';
            $pluginData['Plugin']['registered'] = false;
        }
        return $pluginData;
    }

    /**
     * プラグイン管理のリンクを指定したユーザーのお気に入りに追加
     *
     * @param string $pluginName プラグイン名
     * @param array $user ユーザーデータの配列
     * @return void
     */
    public function addFavoriteAdminLink($pluginName, $user)
    {
        $plugin = $this->findByName($pluginName);
        $dirPath = $this->getDirectoryPath($pluginName);
        $pluginInfo = $this->getPluginInfo([$plugin], $dirPath);

        //リンクが設定されていない
        if (empty($pluginInfo['Plugin']['admin_link'])) {
            return;
        }

        if (ClassRegistry::isKeySet('Favorite')) {
            $this->Favorite = ClassRegistry::getObject('Favorite');
        } else {
            $this->Favorite = ClassRegistry::init('Favorite');
        }

        $adminLinkUrl = Router::url($pluginInfo['Plugin']['admin_link']);
        if (isset($pluginInfo['Plugin']['admin_link']['action']) &&
            $pluginInfo['Plugin']['admin_link']['action'] == 'index') {
            $adminLinkUrl .= '/';
        }
        $baseUrl = Configure::read('App.baseUrl');
        if ($baseUrl) {
            $adminLinkUrl = preg_replace('/^' . preg_quote($baseUrl, '/') . '/', '', $adminLinkUrl);
        }
        $request = Router::getRequest();
        if ($request) {
            $base = $request->base;
            if ($request->base) {
                $adminLinkUrl = preg_replace('/^' . preg_quote($request->base, '/') . '/', '', $adminLinkUrl);
            }
        }

        //すでにお気に入りにリンクが含まれている場合
        if ($this->Favorite->find('count', ['conditions' => ['Favorite.url' => $adminLinkUrl, 'Favorite.user_id' => $user['id']]]) > 0) {
            return;
        }

        $favorite = [
            'name' => sprintf('%s 管理', $pluginInfo['Plugin']['title']),
            'url' => $adminLinkUrl,
            'sort' => $this->Favorite->getMax('sort') + 1,
            'user_id' => $user['id'],
        ];

        $this->Favorite->create($favorite);
        $this->Favorite->save();
    }

}
