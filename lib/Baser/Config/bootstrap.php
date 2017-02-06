<?php
/**
 * 起動スクリプト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
require CORE_PATH . 'Baser' . DS . 'Config' . DS . 'paths.php';
require BASER . 'basics.php';
require BASER . 'Error' . DS . 'exceptions.php';

/**
 * インストール状態
 */
define('BC_INSTALLED', isInstalled());

/**
 * Baserパス追加
 */
//優先度高
App::build(array(
	'View'						=> array(WWW_ROOT),
), App::PREPEND);


//優先度低
App::build(array(
	'Controller'				=> array(BASER_CONTROLLERS),
	'Model'						=> array(BASER_MODELS),
	'Model/Behavior'			=> array(BASER_BEHAVIORS),
	'Model/Datasource'			=> array(BASER_DATASOURCE),
	'Model/Datasource/Database' => array(BASER_DATABASE),
	'Controller/Component'		=> array(BASER_COMPONENTS),
	'Controller/Component/Auth' => array(BASER_COMPONENTS . 'Auth' . DS),
	'View'						=> array(BASER_VIEWS),
	'View/Helper'				=> array(BASER_HELPERS),
	'Plugin'					=> array(BASER_PLUGINS),
	'Vendor'					=> array(BASER_VENDORS),
	'Locale'					=> array(BASER_LOCALES),
	'Lib'						=> array(BASER_LIBS),
	'Console'					=> array(BASER_CONSOLES),
	'Console/Command'			=> array(BASER_CONSOLES . 'Command' . DS),
), App::APPEND);

//新規登録
App::build(array(
	'Event'						=> array(APP . 'Event', BASER_EVENTS),
	'Routing/Filter'			=> array(BASER . 'Routing' . DS . 'Filter' . DS),
	'Configure'					=> array(BASER . 'Configure' . DS),
	'TestSuite'					=> array(BASER_TEST_SUITE),
	'TestSuite/Reporter'		=> array(BASER_TEST_SUITE . 'Reporter' . DS),
	'TestSuite/Fixture'			=> array(BASER_TEST_SUITE . 'Fixture' . DS),
	'Network'					=> array(BASER . 'Network' . DS)
), App::REGISTER);

/**
 * 配置パターン
 * Windows対策として、「\」を「/」へ変換してチェックする
 */
if (!defined('BC_DEPLOY_PATTERN')) {
	if (!preg_match('/' . preg_quote(str_replace('\\', '/', docRoot()), '/') . '/', str_replace('\\', '/', ROOT))) {
		// CakePHP標準の配置
		define('BC_DEPLOY_PATTERN', 3);
	} elseif (ROOT . DS == WWW_ROOT) {
		// webrootをドキュメントルートにして、その中に app / baser / cake を配置
		define('BC_DEPLOY_PATTERN', 2);
	} else {
		// baserCMS配布時の配置
		define('BC_DEPLOY_PATTERN', 1);
	}
}

/**
 * baserUrl取得
 */
define('BC_BASE_URL', baseUrl());

/**
 * ディスパッチャーフィルターを追加
 */
$filters = Configure::read('Dispatcher.filters');
if (!is_array($filters)) {
	$filters = array();
}
Configure::write('Dispatcher.filters',
	array_merge(
		$filters,
		array(
			'BcAssetDispatcher',
			'BcCacheDispatcher',
			'BcRequestFilter'
		)
	)
);

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
App::uses('BcPluginAppController', 'Controller');
App::uses('BcPluginAppModel', 'Model');
App::uses('BcManagerShell', 'Console/Command');
App::uses('CakeRequest', 'Network');

/**
 * 設定ファイル読み込み
 * install.php で設定している為、一旦読み込んで再設定
 */
$baserSettings = array();
$baserSettings['BcEnv'] = Configure::read('BcEnv');
$baserSettings['BcApp'] = Configure::read('BcApp');
Configure::config('baser', new PhpReader(BASER_CONFIGS));
if (Configure::load('setting', 'baser') === false) {
	$config = array();
	include BASER_CONFIGS . 'setting.php';
	Configure::write($config);
}
if (BC_INSTALLED && $baserSettings) {
	foreach ($baserSettings as $key1 => $settings) {
		if ($settings) {
			foreach ($settings as $key2 => $setting) {
				Configure::write($key1 . '.' . $key2, $setting);
			}
		}
	}
}

/**
 * 静的ファイルの読み込みの場合はスキップ
 */
$assetRegex = '/^' . preg_quote(BC_BASE_URL, '/') . '(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
$assetRegexTheme = '/^' . preg_quote(BC_BASE_URL, '/') . 'theme\/[^\/]+?\/(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
$uri = @$_SERVER['REQUEST_URI'];
if (preg_match($assetRegex, $uri) || preg_match($assetRegexTheme, $uri)) {
	Configure::write('BcRequest.asset', true);
	return;
}

/**
 * セッション設定
 */
if (BC_INSTALLED) {
	require APP . 'Config' . DS . 'session.php';
}

/**
 * パラメーター取得
 */
$url = getUrlFromEnv(); // 環境変数からパラメータを取得
$parameter = getUrlParamFromEnv();
Configure::write('BcRequest.pureUrl', $parameter); // ※ requestActionに対応する為、routes.php で上書きされる

if (BC_INSTALLED) {
/**
 * tmpフォルダ確認
 */
	checkTmpFolders();

/**
 * Configures default file logging options
 */
	App::uses('CakeLog', 'Log');
	CakeLog::config('debug', array(
		'engine' => 'FileLog',
		'types' => array('notice', 'info', 'debug'),
		'file' => 'debug',
	));
	CakeLog::config('error', array(
		'engine' => 'FileLog',
		'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
		'file' => 'error',
	));
	CakeLog::config('update', array(
		'engine' => 'FileLog',
		'types' => array('update'),
		'file' => 'update',
	));

/**
 * キャッシュ設定
 */
	$cacheEngine = Configure::read('BcCache.engine');
	$cachePrefix = Configure::read('BcCache.prefix');
	$cacheDuration = Configure::read('BcCache.duration');

	// モデルスキーマ
	Cache::config('_cake_model_', array(
		'engine' => $cacheEngine,
		'prefix' => $cachePrefix . 'cake_model_',
		'path' => CACHE . 'models' . DS,
		'serialize' => ($cacheEngine === 'File'),
		'duration' => $cacheDuration
	));
	// コア環境
	Cache::config('_cake_core_', array(
		'engine' => $cacheEngine,
		'prefix' => $cachePrefix . 'cake_core_',
		'path' => CACHE . 'persistent' . DS,
		'serialize' => ($cacheEngine === 'File'),
		'duration' => $cacheDuration
	));
	// DBデータキャッシュ
	Cache::config('_cake_data_', array(
		'engine' => $cacheEngine,
		'path' => CACHE . 'datas',
		'probability' => 100,
		'prefix' => $cachePrefix . 'cake_data_',
		'lock' => true,
		'serialize' => ($cacheEngine === 'File'),
		'duration' => $cacheDuration
	));
	// 環境情報キャッシュ
	Cache::config('_cake_env_', array(
		'engine' => $cacheEngine,
		'probability' => 100,
		'path' => CACHE . 'environment',
		'prefix' => $cachePrefix . 'cake_env_',
		'lock' => false,
		'serialize' => ($cacheEngine === 'File'),
		'duration' => $cacheDuration
	));

/**
 * サイト基本設定を読み込む
 * bootstrapではモデルのロードは行わないようにする為ここで読み込む
 */
	loadSiteConfig();

/**
 * メンテナンスチェック
 */
	$isMaintenance = ($parameter == 'maintenance/index');
	Configure::write('BcRequest.isMaintenance', $isMaintenance);

/**
 * アップデートチェック
 */
	$isUpdater = false;
	$bcSite = Configure::read('BcSite');
	$updateKey = preg_quote(Configure::read('BcApp.updateKey'), '/');
	if (preg_match('/^' . $updateKey . '(|\/index\/)/', $parameter)) {
		$isUpdater = true;
	} elseif (BC_INSTALLED && !$isMaintenance && (!empty($bcSite['version']) && (getVersion() > $bcSite['version']))) {
		header('Location: ' . topLevelUrl(false) . baseUrl() . 'maintenance/index');
		exit();
	}
	Configure::write('BcRequest.isUpdater', $isUpdater);
}
/**
 * プラグインをCake側で有効化
 * 
 * カレントテーマのプラグインも読み込む
 */

if (BC_INSTALLED && !$isUpdater && !$isMaintenance) {
	App::build(array('Plugin' => array(BASER_THEMES . $bcSite['theme'] . DS . 'Plugin' . DS)), App::PREPEND);
	$plugins = getEnablePlugins();
	foreach ($plugins as $plugin) {
		loadPlugin($plugin['Plugin']['name'], $plugin['Plugin']['priority']);
	}
	$plugins = Hash::extract($plugins, '{n}.Plugin.name');
	Configure::write('BcStatus.enablePlugins', $plugins);

/**
 * イベント登録
 */
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
	$themePath = WWW_ROOT . 'theme' . DS . Configure::read('BcSite.theme') . DS;
	$themeBootstrap = $themePath . 'Config' . DS . 'bootstrap.php';
	if (file_exists($themeBootstrap)) {
		include $themeBootstrap;
	}
}
/**
 * 文字コードの検出順を指定
 */
mb_detect_order(Configure::read('BcEncode.detectOrder'));
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
if (!isConsole()) {
	$Session = new CakeSession();
	$Session->start();
}

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
		if (isset($_SESSION['Auth']['User'])) {
			Configure::write('Cache.check', false);
		}
	}
} else {
	Configure::write('Cache.check', false);
	clearViewCache();
}

/**
 * テーマヘルパーのパスを追加する 
 */
if (BC_INSTALLED || isConsole()) {
	App::build(array(
		'View/Helper' => array(BASER_THEMES . Configure::read('BcSite.theme') . DS . 'Helper' . DS)
	), App::PREPEND);
}
