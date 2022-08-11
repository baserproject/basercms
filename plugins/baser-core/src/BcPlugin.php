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

namespace BaserCore;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUpdateLog;
use BaserCore\Utility\BcUtil;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\PluginApplicationInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Inflector;
use Migrations\Migrations;
use Cake\Core\Plugin as CakePlugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class plugin
 * @package BaserCore
 */
class BcPlugin extends BasePlugin
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use LogTrait;

    /**
     * @var Migrations
     */
    public $migrations;

    /**
     * Initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->migrations = new Migrations();
    }

    /**
     * bootstrap
     *
     * @param PluginApplicationInterface $application
     */
    public function bootstrap(PluginApplicationInterface $application): void
    {
        $pluginPath = BcUtil::getPluginPath($this->name);
        if (file_exists($pluginPath . 'config' . DS . 'setting.php')) {
            try {
                Configure::config('baser', new PhpConfig());
                Configure::load($this->name . '.setting', 'baser');
            } catch (BcException $e) {
            }
        }
        // 親の bootstrap は、setting の読み込みの後でなければならない
        // bootstrap 内で、setting の値を参照する場合があるため
        parent::bootstrap($application);
    }

    /**
     * プラグインをインストールする
     *
     * マイグレーションファイルを読み込み、 plugins テーブルに登録する
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     * @unitTest
     * @noTodo
     * @checked
     */
    public function install($options = []): bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default'
        ], $options);
        $pluginName = $options['plugin'];
        BcUtil::clearAllCache();
        $pluginPath = BcUtil::getPluginPath($options['plugin']);
        try {
            $plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
            $plugin = $plugins->findByName($pluginName)->first();
            if (!$plugin || !$plugin->db_init) {
                if (is_dir($pluginPath . 'config' . DS . 'Migrations')) {
                    $this->migrations->migrate($options);
                }
                if (is_dir($pluginPath . 'config' . DS . 'Seeds')) {
                    $this->migrations->seed($options);
                }
            }

            $this->createAssetsSymlink();

            BcUtil::clearAllCache();
            return $plugins->install($pluginName);
        } catch (BcException $e) {
            $this->log($e->getMessage());
            $this->migrations->rollback($options);
            return false;
        }

    }

    /**
     * update
     * @param array $options
     * @return bool
     */
    public function update($options = []): bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default'
        ], $options);
        BcUtil::clearAllCache();
        $name = $options['plugin'];
        $plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
        $targetVersion = BcUtil::getVersion($name);
        BcUpdateLog::set(__d('baser', '{0} プラグイン {1} へのアップデートを開始します。', $name, $targetVersion));

        TableRegistry::getTableLocator()->clear();

        try {

            if (is_dir($this->getPath() . 'config' . DS . 'Migrations')) {
                $this->migrations->migrate($options);
            }

            $updaters = $this->getUpdaters();
            if ($updaters) {
                asort($updaters);
                foreach($updaters as $version => $updateVerPoint) {
                    $version = explode('-', $version)[1];
                    BcUpdateLog::set(__d('baser', 'アップデートプログラム {0} を実行します。', $version));
                    $this->execScript($version);
                }
            }

            if (!isset($updaters['test'])) {
                $result = $plugins->update($name, $targetVersion);
            } else {
                $result = true;
            }

            $this->createAssetsSymlink();

            BcUpdateLog::set(__d('baser', '{0} プラグイン {1} へのアップデートが完了しました。', $name, $targetVersion));
            BcUtil::clearAllCache();
            BcUpdateLog::save();
            return $result;
        } catch (BcException $e) {
            BcUpdateLog::set(__d('baser', 'アップデート処理が途中で失敗しました。'));
            BcUpdateLog::set($e->getMessage());
            BcUtil::clearAllCache();
            BcUpdateLog::save();
            $this->migrations->rollback($options);
            return false;
        }

    }

    /**
     * プラグインアセットのシンボリックリンクを作成する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createAssetsSymlink():void
    {
        $command = ROOT . DS . 'bin' . DS . 'cake plugin assets symlink';
        exec($command);
    }

    /**
     * アップデートスクリプトを実行する
     *
     * @param string $__plugin
     * @param string $__version
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function execScript($__version)
    {
        $__path = CakePlugin::path($this->getName()) . DS . 'config' . DS . 'update' . DS . $__version . DS . 'updater.php';
        if (!file_exists($__path)) return true;
        try {
            include $__path;
        } catch (BcException $e) {
            $this->log($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * アップデータのパスを取得する
     *
     * @param string $plugin
     * @return array $updates
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUpdaters($name = '')
    {
        if(!$name) $name = $this->getName();
        $targetVerPoint = BcUtil::verpoint(BcUtil::getVersion($name));
        $sourceVerPoint = BcUtil::verpoint(BcUtil::getDbVersion($name));
        if ($sourceVerPoint === false || $targetVerPoint === false) {
            return [];
        }

        // 有効化されていない可能性があるため CakePlugin::path() は利用しない
        $path = BcUtil::getPluginPath($name) . 'config' . DS . 'update';
        $folder = new Folder($path);
        $files = $folder->read(true, true);
        $updaters = [];
        $updateVerPoints = [];
        if (!empty($files[0])) {
            foreach($files[0] as $folder) {
                $updateVersion = $folder;
                $updateVerPoints[$updateVersion] = BcUtil::verpoint($updateVersion);
            }
            asort($updateVerPoints);
            foreach($updateVerPoints as $key => $updateVerPoint) {
                if (($updateVerPoint > $sourceVerPoint && $updateVerPoint <= $targetVerPoint) || $key === 'test') {
                    if (file_exists($path . DS . $key . DS . 'updater.php')) {
                        $updaters[$name . '-' . $key] = $updateVerPoint;
                    }
                }
            }
        }
        return $updaters;
    }

    /**
     * アップデータのメッセージを取得する
     * 現在のバージョンより上位のアップデートスクリプトフォルダの config.php を読み込み
     * 変数 $updateMessage より取得する
     *
     * 戻り値例
     *  [
     *      '1.0.1 => 'message',
     *      '1.0.2 => 'message'
     *  ]
     *
     * @return array $messages
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUpdateScriptMessages($name = '')
    {
        if(!$name) $name = $this->getName();
        $targetVerPoint = BcUtil::verpoint(BcUtil::getVersion($name));
        $sourceVerPoint = BcUtil::verpoint(BcUtil::getDbVersion($name));
        if ($sourceVerPoint === false || $targetVerPoint === false) {
            return [];
        }

        // 有効化されていない可能性があるため CakePlugin::path() は利用しない
        $path = BcUtil::getPluginPath($name) . 'config' . DS . 'update';
        $folder = new Folder($path);
        $files = $folder->read(true, true);
        $messages = [];
        $updateVerPoints = [];
        if (!empty($files[0])) {
            foreach($files[0] as $folder) {
                $updateVersion = $folder;
                $updateVerPoints[$updateVersion] = BcUtil::verpoint($updateVersion);
            }
            asort($updateVerPoints);
            foreach($updateVerPoints as $key => $updateVerPoint) {
                $updateMessage = '';
                if (($updateVerPoint > $sourceVerPoint && $updateVerPoint <= $targetVerPoint) || $key === 'test') {
                    if (file_exists($path . DS . $key . DS . 'config.php')) {
                        $config = include $path . DS . $key . DS . 'config.php';
                        if (!empty($config['updateMessage'])) {
                            $messages[$name . '-' . $key] = $config['updateMessage'];
                        }
                    }
                }
            }
        }
        return $messages;
    }

    /**
     * プラグインをアンインストールする
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     *  - `target` : ロールバック対象バージョン
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall($options = []): bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default',
            'target' => 0,
        ], $options);
        $pluginName = $options['plugin'];

        $this->rollbackDb($options);

        $pluginPath = BcUtil::getPluginPath($pluginName);
        if ($pluginPath) {
            $Folder = new Folder();
            $Folder->delete($pluginPath);
        }

        $plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
        return $plugins->uninstall($pluginName);
    }

    /**
     * プラグインのテーブルをリセットする
     *
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     *  - `target` : ロールバック対象バージョン
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rollbackDb($options = []): bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default',
            'target' => 0,
        ], $options);
        $pluginName = $options['plugin'];
        try {
            $this->migrations->rollback($options);

            $phinxTableName = Inflector::underscore($pluginName) . '_phinxlog';
            $connection = ConnectionManager::get($options['connection']);
            $schema = $connection->getDriver()->newTableSchema($phinxTableName);
            $sql = $schema->dropSql($connection);
            $connection->execute($sql[0])->closeCursor();
        } catch (BcException $e) {
            return false;
        }
        return true;
    }

    /**
     * @param \Cake\Routing\RouteBuilder $routes
     * @checked
     * @unitTest
     * @noTodo
     */
    public function routes($routes): void
    {
        $plugin = $this->getName();

        /**
         * インストーラー
         */
        if (!Configure::read('BcRequest.isInstalled')) {
            $routes->connect('/', ['plugin' => 'BaserCore', 'controller' => 'Installations', 'action' => 'index']);
            $routes->connect('/install', ['plugin' => 'BaserCore', 'controller' => 'Installations', 'action' => 'index']);
            $routes->fallbacks(InflectedRoute::class);
            parent::routes($routes);
            return;
        }

        /**
         * コンテンツ管理ルーティング
         */
        $routes->plugin(
            $plugin,
            ['path' => '/'],
            function(RouteBuilder $routes) {
                $routes->setRouteClass('BaserCore.BcContentsRoute');
                $routes->connect('/', []);
                $routes->connect('/{controller}/index', []);
                $routes->connect('/:controller/:action/*', []);
            }
        );

        // プラグインの管理画面用ルーティング
        $prefixSettings = Configure::read('BcPrefixAuth');
        foreach($prefixSettings as $prefix => $setting) {
            $routes->prefix(
                $prefix,
                ['path' => '/' . BcUtil::getBaserCorePrefix() . '/' . $setting['alias']],
                function(RouteBuilder $routes) use ($plugin) {
                    $routes->plugin(
                        $plugin,
                        ['path' => '/' . Inflector::dasherize($plugin)],
                        function(RouteBuilder $routes) {
                            // CakePHPのデフォルトで /index が省略する仕様のため、URLを生成する際は、強制的に /index を付ける仕様に変更
                            $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                            $routes->fallbacks(InflectedRoute::class);
                        }
                    );
                }
            );
        }

        // プラグインのフロントエンド用ルーティング
        $routes->plugin(
            $plugin,
            ['path' => '/' . BcUtil:: getBaserCorePrefix() . '/' . Inflector::dasherize($plugin)],
            function(RouteBuilder $routes) {
                // AnalyseController で利用
                $routes->setExtensions(['json']);
                $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                $routes->fallbacks(InflectedRoute::class);
            }
        );

        // API用ルーティング
        $routes->prefix(
            'Api',
            ['path' => '/' . BcUtil::getBaserCorePrefix() . '/api'],
            function(RouteBuilder $routes) use ($plugin) {
                $routes->plugin(
                    $plugin,
                    ['path' => '/' . Inflector::dasherize($plugin)],
                    function(RouteBuilder $routes) {
                        $routes->setExtensions(['json']);
                        $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                        $routes->fallbacks(InflectedRoute::class);
                    }
                );
            }
        );

        parent::routes($routes);
    }

}
