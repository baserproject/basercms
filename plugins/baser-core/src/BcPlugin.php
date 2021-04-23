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

namespace BaserCore;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Migrations\Migrations;
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
     * @var Migrations
     */
    public $migrations;

    /**
     * Initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->migrations = new Migrations();
    }

    /**
     * @param \Cake\Routing\RouteBuilder $routes
     * @checked
     */
    public function routes($routes): void
    {
        $path = '/baser';
        Router::plugin(
            $this->getName(),
            ['path' => $path],
            function(RouteBuilder $routes) {
                $path = env('BC_ADMIN_PREFIX', '/admin');
                if ($this->getName() !== 'BaserCore') {
                    $path .= '/' . Inflector::dasherize($this->getName());
                }

                /**
                 * AnalyseController で利用
                 */
                $routes->setExtensions(['json']);
                $routes->fallbacks(InflectedRoute::class);

                $routes->prefix(
                    'Admin',
                    ['path' => $path],
                    function(RouteBuilder $routes) {
                        $routes->connect('', ['controller' => 'Dashboard', 'action' => 'index']);
                        // CakePHPのデフォルトで /index が省略する仕様のため、URLを生成する際は、強制的に /index を付ける仕様に変更
                        $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                        $routes->fallbacks(InflectedRoute::class);
                    }
                );
            }
        );
        parent::routes($routes);
    }

    /**
     * プラグインをインストールする
     *
     * マイグレーションファイルを読み込み、 plugins テーブルに登録する
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     */
    public function install($options = []) : bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default'
        ], $options);
        $pluginName = $options['plugin'];

        // TODO clearAllCache 未実装
        // clearAllCache();
        $pluginPath = BcUtil::getPluginPath($options['plugin']);
        try {
            $plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
            $plugin = $plugins->findByName($pluginName)->first();
            if(!$plugin || !$plugin->db_init) {
                if (is_dir($pluginPath . 'config' . DS . 'Migrations')) {
                    $this->migrations->migrate($options);
                }
                if (is_dir($pluginPath . 'config' . DS . 'Seeds')) {
                    $this->migrations->seed($options);
                }
            }
            return $plugins->install($pluginName);
        } catch (BcException $e) {
            $this->migrations->rollback($options);
            return false;
        }

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
    public function uninstall($options = []) : bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default',
            'target' => 0,
        ], $options);
        $pluginName = $options['plugin'];

        $this->rollbackDb($options);

        $pluginPath = BcUtil::getPluginPath($pluginName);
        if($pluginPath) {
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
    public function rollbackDb($options = []) : bool
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

}
