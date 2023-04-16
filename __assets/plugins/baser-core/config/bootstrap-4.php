<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\Service\BcFrontService;
use BaserCore\Utility\BcUtil;

require CORE_PATH . 'Baser' . DS . 'Config' . DS . 'paths.php';
require BASER . 'basics.php';
require BASER . 'Error' . DS . 'exceptions.php';

/**
 * Baserパス追加
 */
//優先度高
App::build([
    'View' => [WWW_ROOT],
], App::PREPEND);


//優先度低
App::build([
    'Controller' => [BASER_CONTROLLERS],
    'Model' => [BASER_MODELS],
    'Model/Behavior' => [BASER_BEHAVIORS],
    'Model/Datasource' => [BASER_DATASOURCE],
    'Model/Datasource/Database' => [BASER_DATABASE],
    'Controller/Component' => [BASER_COMPONENTS],
    'Controller/Component/Auth' => [BASER_COMPONENTS . 'Auth' . DS],
    'View' => [BASER_VIEWS],
    'View/Helper' => [BASER_HELPERS],
    'Plugin' => [BASER_PLUGINS],
    'Vendor' => [BASER_VENDORS],
    'Locale' => [BASER_LOCALES],
    'Lib' => [BASER_LIBS],
    'Console' => [BASER_CONSOLES],
    'Console/Command' => [BASER_CONSOLES . 'Command' . DS],
], App::APPEND);

//新規登録
App::build([
    'Event' => [APP . 'Event', BASER_EVENTS],
    'Routing' => [BASER . 'Routing' . DS],
    'Routing/Filter' => [BASER . 'Routing' . DS . 'Filter' . DS],
    'Routing/Route' => [BASER . 'Routing' . DS . 'Route' . DS],
    'Configure' => [BASER . 'Configure' . DS],
    'TestSuite' => [BASER_TEST_SUITE],
    'TestSuite/Reporter' => [BASER_TEST_SUITE . 'Reporter' . DS],
    'TestSuite/Fixture' => [BASER_TEST_SUITE . 'Fixture' . DS],
    'Network' => [BASER . 'Network' . DS]
], App::REGISTER);

/**
 * ディスパッチャーフィルターを追加
 */
$filters = Configure::read('Dispatcher.filters');
if (!is_array($filters)) {
    $filters = [];
}
Configure::write('Dispatcher.filters',
    array_merge(
        $filters,
        [
            'BcAssetDispatcher',
            'BcCacheDispatcher',
            'BcRequestFilter',
            'BcRedirectMainSiteFilter',
            'BcRedirectSubSiteFilter'
        ]
    )
);

/**
 * baserUrl取得
 * BC_DEPLOY_PATTERN の定義より後に実行
 */
define('BC_BASE_URL', BcUtil::baseUrl());

/**
 * 静的ファイルの読み込みの場合はスキップ
 */
$assetRegex = '/^' . preg_quote(BC_BASE_URL, '/') . '.*?(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
$assetRegexTheme = '/^' . preg_quote(BC_BASE_URL, '/') . 'theme\/[^\/]+?\/(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
// テーマ編集は除外
$nonAssets = '/^' . preg_quote(BC_BASE_URL . Configure::read('Routing.prefixes.0') . '/theme_files/edit/', '/') . '.*?(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
$uri = @$_SERVER['REQUEST_URI'];
if (preg_match($nonAssets, $uri) === 0) {
    if (preg_match($assetRegex, $uri) || preg_match($assetRegexTheme, $uri)) {
        Configure::write('BcRequest.asset', true);
        App::uses('ClassRegistry', 'Utility');
        $plugins = getEnablePlugins();
        foreach($plugins as $plugin) {
            // プラグインのパスを取得するため２回ロード
            CakePlugin::load($plugin['Plugin']['name']);
            CakePlugin::load($plugin['Plugin']['name'], [
                'bootstrap' => file_exists(CakePlugin::path($plugin['Plugin']['name']) . 'Config' . DS . 'bootstrap.php')
            ]);
        }
    }
}

/**
 * クラスローダー設定
 */
App::uses('AppModel', 'Model');
App::uses('BcAppModel', 'Model');
App::uses('BcCache', 'Model/Behavior');
App::uses('ClassRegistry', 'Utility');
App::uses('Multibyte', 'I18n');
App::uses('BcCsv', 'Model/Datasource/Database');
App::uses('BcPostgres', 'Model/Datasource/Database');
App::uses('BcSqlite', 'Model/Datasource/Database');
App::uses('BcMysql', 'Model/Datasource/Database');
App::uses('PhpReader', 'Configure');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('BcUtil', 'Lib');
App::uses('BcControllerEventListener', 'Event');
App::uses('BcModelEventListener', 'Event');
App::uses('BcViewEventListener', 'Event');
App::uses('BcHelperEventListener', 'Event');
App::uses('BcManagerShell', 'Console/Command');
App::uses('CakeRequest', 'Network');
App::uses('BcSite', 'Lib');
App::uses('BcAgent', 'Lib');
App::uses('BcLang', 'Lib');

/**
 * 設定ファイル読み込み
 * install.php で設定している為、一旦読み込んで再設定
 */
$baserSettings = [];
$baserSettings['BcEnv'] = Configure::read('BcEnv');
$baserSettings['BcApp'] = Configure::read('BcApp');
Configure::config('baser', new PhpReader(BASER_CONFIGS));
if (Configure::load('setting', 'baser') === false) {
    $config = [];
    include BASER_CONFIGS . 'setting.php';
    Configure::write($config);
}
if (BcUtil::isInstalled() && $baserSettings) {
    foreach($baserSettings as $key1 => $settings) {
        if ($settings) {
            foreach($settings as $key2 => $setting) {
                Configure::write($key1 . '.' . $key2, $setting);
            }
        }
    }
}

/**
 * セッション設定
 */
if (BcUtil::isInstalled()) {
    require APP . 'Config' . DS . 'session.php';
}

/**
 * パラメーター取得
 */
$parameter = getUrlParamFromEnv();

if (BcUtil::isInstalled()) {

    /**
     * キャッシュ設定
     */
    $cacheEngine = Configure::read('BcCache.engine');
    $cachePrefix = Configure::read('BcCache.prefix');
    $cacheDuration = Configure::read('BcCache.duration');

    // モデルスキーマ
    Cache::config('_cake_model_', [
        'engine' => $cacheEngine,
        'prefix' => $cachePrefix . 'cake_model_',
        'path' => CACHE . 'models' . DS,
        'duration' => $cacheDuration
    ]);
    // コア環境
    Cache::config('_cake_core_', [
        'engine' => $cacheEngine,
        'prefix' => $cachePrefix . 'cake_core_',
        'path' => CACHE . 'persistent' . DS,
        'duration' => $cacheDuration
    ]);
    // DBデータキャッシュ
    Cache::config('_cake_data_', [
        'engine' => $cacheEngine,
        'path' => CACHE . 'datas',
        'probability' => 100,
        'prefix' => $cachePrefix . 'cake_data_',
        'lock' => true,
        'duration' => $cacheDuration
    ]);
    // エレメントキャッシュ
    Cache::config('_cake_element_', [
        'engine' => $cacheEngine,
        'path' => CACHE . 'views',
        'probability' => 100,
//		'prefix' => $cachePrefix . 'cake_data_',
        'lock' => true,
        'duration' => Configure::read('BcCache.viewDuration')
    ]);
    // 環境情報キャッシュ
    Cache::config('_bc_env_', [
        'engine' => $cacheEngine,
        'probability' => 100,
        'path' => CACHE . 'environment',
        'prefix' => $cachePrefix . 'cake_env_',
        'lock' => false,
        'duration' => $cacheDuration
    ]);

    /**
     * サイト基本設定を読み込む
     * bootstrapではモデルのロードは行わないようにする為ここで読み込む
     */
    if (empty($_GET['requestview']) || $_GET['requestview'] != 'false') {
        loadSiteConfig();
    }
}

/**
 * テーマヘルパーのパスを追加する
 */
if (BcUtil::isInstalled() || isConsole()) {
    App::build([
        'View/Helper' => [BASER_THEMES . Configure::read('BcSite.theme') . DS . 'Helper' . DS]
    ], App::PREPEND);
}

/**
 * プラグインをCake側で有効化
 *
 * カレントテーマのプラグインも読み込む
 * サブサイトに適用されているプラグインも読み込む
 */

if (BcUtil::isInstalled() && !$isUpdater && !$isMaintenance) {
    $sitesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
    $sites = $sitesTable->find()->all();
    $pluginPaths = [ROOT . DS . 'Plugin' . DS];
    foreach($sites as $site) {
        if ($site->theme) {
            $pluginPaths[] = BASER_THEMES . $site->theme . DS . 'Plugin' . DS;
        }
    }
    App::build(['Plugin' => $pluginPaths], App::PREPEND);
    $plugins = getEnablePlugins();
    foreach($plugins as $plugin) {
        loadPlugin($plugin['Plugin']['name'], $plugin['Plugin']['priority']);
    }

    /**
     * アセットの場合負荷を軽減するため以降の処理を終了
     */
    if (Configure::read('BcRequest.asset')) {
        return;
    }

    /**
     * イベント登録
     */
    App::uses('CakeEventManager', 'Event');
    App::uses('BcControllerEventDispatcher', 'Event');
    App::uses('BcModelEventDispatcher', 'Event');
    App::uses('BcViewEventDispatcher', 'Event');
    $CakeEvent = CakeEventManager::instance();
    $CakeEvent->attach(new BcControllerEventDispatcher());
    $CakeEvent->attach(new BcModelEventDispatcher());
    $CakeEvent->attach(new BcViewEventDispatcher());

    /**
     * テーマの bootstrap を実行する
     */
    if (!BcUtil::isAdminSystem($parameter)) {
        $themePath = WWW_ROOT . 'theme' . DS . Configure::read('BcSite.theme') . DS;
        $themeBootstrap = $themePath . 'Config' . DS . 'bootstrap.php';
        if (file_exists($themeBootstrap)) {
            include $themeBootstrap;
        }
    }
}

/**
 * メモリー設定
 */
$memoryLimit = (int)ini_get('memory_limit');
if ($memoryLimit < 32 && $memoryLimit != -1) {
    ini_set('memory_limit', '32M');
}

/**
 * ロケール設定
 * 指定しないと 日本語入りの basename 等が失敗する
 */
setlocale(LC_ALL, 'ja_JP.UTF-8');

/**
 * セッションスタート
 */
$Session = new CakeSession();
$Session->start();


/**
 * Viewのキャッシュ設定・ログの設定
 */
if (Configure::read('debug') == 0) {
    if (isset($_SESSION) && session_id()) {
        // 管理ユーザーでログインしている場合、ページ機能の編集ページへのリンクを表示する為、キャッシュをオフにする。
        // ただし、現在の仕様としては、セッションでチェックしているので、ブラウザを閉じてしまった場合、一度管理画面を表示する必要がある。
        // TODO ブラウザを閉じても最初から編集ページへのリンクを表示する場合は、クッキーのチェックを行い、認証処理を行う必要があるが、
        // セキュリティ上の問題もあるので実装は検討が必要。
        // bootstrapで実装した場合、他ページへの負荷の問題もある
        if (isset($_SESSION['Auth'][Configure::read('BcPrefixAuth.Admin.sessionKey')])) {
            Configure::write('Cache.check', false);
        }
    }
} else {
    Configure::write('Cache.check', false);
    clearViewCache();
}

// サブサイトの際にキャッシュがメインサイトと重複しないように調整
if (Configure::read('Cache.check')) {
    $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
    $site = $sites->findByUrl($_SERVER['REQUEST_URI']);
    if ($site->use_subdomain) {
        Configure::write('Cache.viewPrefix', $site->alias);
    }
}

