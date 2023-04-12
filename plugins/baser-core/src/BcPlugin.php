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
use BaserCore\Model\Entity\Site;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\PermissionGroupsService;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUpdateLog;
use BaserCore\Utility\BcUtil;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\PluginApplicationInterface;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Http\ServerRequestFactory;
use Cake\I18n\FrozenTime;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Migrations\Migrations;
use Cake\Core\Plugin as CakePlugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class plugin
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
     *  - `permission` : アクセスルールを作るか作らないか。作らない場合は、システム管理ユーザーが利用可能
     * @unitTest
     * @noTodo
     * @checked
     */
    public function install($options = []): bool
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default',
            'permission' => false
        ], $options);
        $pluginName = $options['plugin'];
        $permission = $options['permission'];
        unset($options['permission']);
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
                    $dbService = $this->getService(BcDatabaseServiceInterface::class);
                    $dbService->updateSequence();
                }
            }

            // アクセスルールを作成
            if($permission) {
                /** @var PermissionGroupsService $permissionGroupsService */
                $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
                $permissionGroupsService->buildByPlugin($pluginName);
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
     * マイグレーションを実行する
     *
     * @param array $options
     */
    public function migrate($options = [])
    {
        $options = array_merge([
            'plugin' => $this->getName(),
            'connection' => 'default'
        ], $options);
        if (is_dir($this->getPath() . 'config' . DS . 'Migrations')) {
            $this->migrations->migrate($options);
        }
    }

    /**
     * アップデートプログラムを実行する
     */
    public function execUpdater()
    {
        $updaters = $this->getUpdaters();
        if ($updaters) {
            asort($updaters);
            try {
                foreach($updaters as $version => $updateVerPoint) {
                    $version = explode('-', $version)[1];
                    BcUpdateLog::set(__d('baser_core', 'アップデートプログラム {0} を実行します。', $version));
                    $this->execScript($version);
                }
            } catch (\Throwable $e) {
                throw new BcException($e->getMessage());
            }
        }
    }

    /**
     * プラグインアセットのシンボリックリンクを作成する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createAssetsSymlink(): void
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
        $__path = CakePlugin::path($this->getName()) . 'config' . DS . 'update' . DS . $__version . DS . 'updater.php';
        if (!file_exists($__path)) return true;
        try {
            include $__path;
        } catch (\Throwable $e) {
            throw new BcException($e->getMessage());
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
        if (!$name) $name = $this->getName();
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
        if (!$name) $name = $this->getName();
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
        /** @var PermissionGroupsService $permissionGroupsService */
        $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
        $permissionGroupsService->deleteByPlugin($pluginName);

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
     * ルーティング設定
     *
     * 次のルートを設定するが、未インストールの場合はスキップする。
     *
     * ### コンテンツ管理のプラグイン用のリバースルーティング
     * ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index'] → /news/
     * ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 1] → /news/archives/1
     *
     * ### 管理画面のプラグイン用ルーティング
     * /baser/admin/plugin-name/controller_name/index
     * /baser/admin/plugin-name/controller_name/action_name/*
     *
     * ### フロントエンドのプラグイン用ルーティング
     * /plugin-name/controller_name/index
     * /plugin-name/controller_name/action_name/*
     *
     * ### サブサイトのプラグイン用ルーティング
     * /site_alias/plugin-name/controller_name/index
     * /site_alias/plugin-name/controller_name/action_name/*
     *
     * ### APIのプラグイン用ルーティング
     * /baser/api/plugin-name/controller_name/index.json
     * /baser/api/plugin-name/controller_name/action_name/*.json
     *
     * @param \Cake\Routing\RouteBuilder $routes
     * @checked
     * @unitTest
     * @noTodo
     */
    public function routes(RouteBuilder $routes): void
    {
        $plugin = $this->getName();

        if (BcUtil::isInstalled() && !BcUtil::isMigrations()) {
            // コンテンツルーティング（リバースルーティング用）
            $routes = $this->contentsRoutingForReverse($routes, $plugin);
            // フロントページルーティング
            $routes = $this->frontPageRouting($routes, $plugin);
        }

        // プレフィックスルーティング
        $routes = $this->prefixRouting($routes, $plugin);

        if (BcUtil::isInstalled() && !BcUtil::isMigrations()) {
            // サイトルーティング
            $routes = $this->siteRouting($routes, $plugin);
        }

        parent::routes($routes);
    }

    /**
     * コンテンツ管理ルーティング
     *
     * リバースルーティングのために必要
     *
     * @param RouteBuilder $routes
     * @param string $plugin
     * @return RouteBuilder
     */
    public function contentsRoutingForReverse(RouteBuilder $routes, string $plugin)
    {
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
        return $routes;
    }

    /**
     * プラグインのフロントエンド用ルーティング（プラグイン名がダッシュ区切りの場合）
     *
     * BcPrefixAuthより先に定義が必要
     * 定義しない場合、CSRFトークン取得などの処理にて、BcPrefixAuthのルーティングに捕まり認証を求められてしまう場合がある。
     * 例）認証付 MyPage の定義で、alias を '/' とした場合
     *
     * @param RouteBuilder $routes
     * @param string $plugin
     * @return RouteBuilder
     */
    public function frontPageRouting(RouteBuilder $routes, string $plugin)
    {

        $routes->plugin(
            $plugin,
            ['path' => '/' . Inflector::dasherize($plugin)],
            function(RouteBuilder $routes) {
                $routes->setExtensions(['json']);   // AnalyseController で利用
                $routes->connect('/{controller}/index', ['sitePrefix' => ''], ['routeClass' => InflectedRoute::class]);
                $routes->connect('/{controller}/{action}/*', ['sitePrefix' => ''], ['routeClass' => InflectedRoute::class]);
                $routes->fallbacks(InflectedRoute::class);
            }
        );
        return $routes;
    }

    /**
     * プラグインの管理画面用ルーティング
     *
     * プラグイン名がダッシュ区切りの場合
     *
     * @param RouteBuilder $routes
     * @param string $plugin
     * @return RouteBuilder
     */
    public function prefixRouting(RouteBuilder $routes, string $plugin)
    {
        $prefixSettings = Configure::read('BcPrefixAuth');
        foreach($prefixSettings as $prefix => $setting) {
            if($prefix === 'Front') continue;
            if(!empty($setting['disabled'])) continue;
            if(empty($setting['alias'])) throw new BcException(__d('baser_core', 'BcPrefixAuth の {0} で alias が指定されていません。', $prefix));
            $isApi = !empty($setting['isRestApi']);
            if(!empty($setting['withCorePrefix'])) {
                $path = '/' . BcUtil::getBaserCorePrefix() . $setting['alias'];
            } elseif($setting['alias'] !== '/') {
                $path = '/' . $setting['alias'];
            } else {
                $path = $setting['alias'];
            }
            $routes->prefix(
                $prefix,
                ['path' => $path],
                function(RouteBuilder $routes) use ($plugin, $isApi) {
                    $routes->plugin(
                        $plugin,
                        ['path' => '/' . Inflector::dasherize($plugin)],
                        function(RouteBuilder $routes) use($isApi) {
                            if($isApi) {
                                $routes->setExtensions(['json']);
                                $routes->resources('{controller}', ['connectOptions' => ['routeClass' => InflectedRoute::class]]);
                            }
                            // CakePHPのデフォルトで /index が省略する仕様のため、URLを生成する際は、強制的に /index を付ける仕様に変更
                            $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                            $routes->fallbacks(InflectedRoute::class);
                        }
                    );
                }
            );
        }
        return $routes;
    }

    /**
     * サブサイトのプラグイン用ルーティング
     *
     * プラグイン名がダッシュ区切りの場合
     *
     * @param RouteBuilder $routes
     * @param string $plugin
     * @return RouteBuilder
     */
    public function siteRouting(RouteBuilder $routes, string $plugin)
    {
        $request = Router::getRequest();
        if (!$request) {
            $request = ServerRequestFactory::fromGlobals();
        }
        /* @var SitesTable $sitesTable */
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        /* @var Site $site */
        $site = $sitesTable->findByUrl($request->getPath());
        if ($site && $site->alias) {
            $routes->plugin(
                $plugin,
                ['path' => '/' . $site->alias . '/' . Inflector::dasherize($plugin)],
                function(RouteBuilder $routes) use ($site) {
                    // BcFrontMiddleware にて、sitePrefix によって currentSite を設定
                    $routes->connect('/{controller}/index', ['sitePrefix' => $site->alias], ['routeClass' => InflectedRoute::class]);
                    $routes->connect('/{controller}/{action}/*', ['sitePrefix' => $site->alias], ['routeClass' => InflectedRoute::class]);
                }
            );
        }
        return $routes;
    }

    /**
     * テーマを適用する
     * @param Site $site
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function applyAsTheme(Site $site, string $theme)
    {
        $site->theme = $theme;
        $siteConfigsTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $siteConfigsTable->save($site);
    }

    /**
     * 対象のフィールドを現在の日付に更新する
     *
     * @param string $table
     * @param string $field
     * @return void
     * @checked
     * @noTodo
     */
    public function updateDateNow(string $table, array $fields, array $conditions = []): void
    {
        $table = TableRegistry::getTableLocator()->get($table);
        $entities = $table->find()->where($conditions)->all();
        if($entities->count()) {
            foreach($entities as $entity) {
                $array = $entity->toArray();
                foreach($fields as $field) {
                    $array[$field] = FrozenTime::now();
                }
                $entity = $table->patchEntity($entity, $array);
                $table->save($entity);
            }
        }
    }

}
