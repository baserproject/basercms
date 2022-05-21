<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

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
	'Error' => [BASER_LIBS . DS . 'Error' . DS],
	'Log/Engine' => [BASER_LIBS . DS . 'Log' . DS . 'Engine' . DS],
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
 * BC_DEPLOY_PATTERN の定義より後に実行
 */
define('BC_BASE_URL', baseUrl());

/**
 * インストール状態
 */
define('BC_INSTALLED', isInstalled());
Configure::write('BcRequest.isInstalled', BC_INSTALLED); // UnitTest用

/**
 * 静的ファイルの読み込みの場合はスキップ
 */
$assetRegex = '/^' . preg_quote(BC_BASE_URL, '/') . '.*?(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
$assetRegexTheme = '/^' . preg_quote(BC_BASE_URL, '/') . 'theme\/[^\/]+?\/(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
// テーマ編集は除外
$nonAssets = '/^' . preg_quote(BC_BASE_URL . Configure::read('Routing.prefixes.0') . '/theme_files/edit/', '/') . '.*?(css|js|img)' . '\/.+\.(js|css|gif|jpg|jpeg|png)$/';
$uri = null;
if (isset($_SERVER['REQUEST_URI'])) {
	$uri = $_SERVER['REQUEST_URI'];
}
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
App::uses('BcFileLog', 'Log/Engine');
App::uses('BcErrorHandler', 'Error');

// @deprecated
// >>>
App::uses('BcPluginAppController', 'Controller');
App::uses('BcPluginAppModel', 'Model');
// <<<

/**
 * 言語設定
 * ブラウザよりベースとなる言語を設定
 */
$baseLang = 'ja';
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$baseLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
}
Configure::write('Config.language', BcLang::parseLang($baseLang));

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
if (BC_INSTALLED && $baserSettings) {
	foreach($baserSettings as $key1 => $settings) {
		if ($settings) {
			foreach($settings as $key2 => $setting) {
				Configure::write($key1 . '.' . $key2, $setting);
			}
		}
	}
}

/**
 * フロントページ用言語設定
 * systemMessageLangFromSiteSetting を読み込んだ上で言語設定を再設定する
 */
$currentSite = BcSite::findCurrent();
if ($currentSite) {
	$lang = Configure::read('BcLang.' . $currentSite->lang);
}
if (Configure::read('BcApp.systemMessageLangFromSiteSetting') && isset($lang['langs'][0])) {
	Configure::write('Config.language', $lang['langs'][0]);
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
$parameter = getUrlParamFromEnv();

if (BC_INSTALLED) {
	/**
	 * tmpフォルダ確認
	 */
	checkTmpFolders();

	/**
	 * Configures default file logging options
	 */
	App::uses('CakeLog', 'Log');
	CakeLog::config('debug', [
		'engine' => 'BcFile',
		'types' => ['notice', 'info', 'debug'],
		'file' => 'debug',
	]);
	CakeLog::config('warning', [
		'engine' => 'BcFile',
		'types' => ['warning'],
		'file' => 'warning',
	]);
	CakeLog::config('error', [
		'engine' => 'BcFile',
		'types' => ['error', 'critical', 'alert', 'emergency'],
		'file' => 'error',
	]);
	CakeLog::config('error404', [
		'engine' => 'BcFile',
		'types' => ['error404'],
		'file' => 'error404',
	]);
	CakeLog::config('update', [
		'engine' => 'FileLog',
		'types' => ['update'],
		'file' => 'update',
	]);

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
	Cache::config('_cake_env_', [
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
		if (!isConsole()) {
			CakeLog::write(LOG_ERR, 'プログラムとデータベースのバージョンが異なります。');
			header('Location: ' . topLevelUrl(false) . baseUrl() . 'maintenance/index');
			exit();
		} else {
			throw new BcException(__d('baser', 'プログラムとデータベースのバージョンが異なるため、強制終了します。データベースのバージョンを調整して、再実行してください。'));
		}
	}
	Configure::write('BcRequest.isUpdater', $isUpdater);
}

/**
 * テーマヘルパーのパスを追加する
 */
if (BC_INSTALLED || isConsole()) {
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

if (BC_INSTALLED && !$isUpdater && !$isMaintenance) {
	$sites = BcSite::findAll();
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
	$plugins = Hash::extract($plugins, '{n}.Plugin.name');
	Configure::write('BcStatus.enablePlugins', $plugins);

	/**
	 * アセットの場合負荷を軽減するため以降の処理を終了
	 */
	if(Configure::read('BcRequest.asset')) {
		return;
	}

	/**
	 * イベント登録
	 */
	App::uses('CakeEventManager', 'Event');
	App::uses('BcControllerEventDispatcher', 'Event');
	App::uses('BcModelEventDispatcher', 'Event');
	App::uses('BcViewEventDispatcher', 'Event');
	App::uses('PagesControllerEventListener', 'Event');
	App::uses('ContentFoldersControllerEventListener', 'Event');
	$CakeEvent = CakeEventManager::instance();
	$CakeEvent->attach(new BcControllerEventDispatcher());
	$CakeEvent->attach(new BcModelEventDispatcher());
	$CakeEvent->attach(new BcViewEventDispatcher());
	$CakeEvent->attach(new PagesControllerEventListener());
	$CakeEvent->attach(new ContentFoldersControllerEventListener());

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
 * 文字コードの検出順を指定
 */
if (function_exists('mb_detect_order')) {
	mb_detect_order(Configure::read('BcEncode.detectOrder'));
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
		if (isset($_SESSION['Auth'][Configure::read('BcAuthPrefix.admin.sessionKey')])) {
			Configure::write('Cache.check', false);
		}
	}
} else {
	Configure::write('Cache.check', false);
	clearViewCache();
}

// サブサイトの際にキャッシュがメインサイトと重複しないように調整
if (Configure::read('Cache.check')) {
	$site = BcSite::findCurrent();
	if ($site->useSubDomain) {
		Configure::write('Cache.viewPrefix', $site->alias);
	}
}

/**
 * 後方互換のため過去テーマ用のアイコンを設定
 * @deprecated 5.0.0 since 4.2.0 過去テーマを廃止予定
 */
if (Configure::read('BcSite.admin_theme') === '') {
	Configure::write('BcContents.items.Core.ContentFolder.icon', 'admin/icon_folder.png');
	Configure::write('BcContents.items.Core.ContentAlias.icon', 'admin/icon_alias.png');
	Configure::write('BcContents.items.Core.ContentLink.icon', 'admin/icon_link.png');
	Configure::write('BcContents.items.Core.Page.icon', 'admin/icon_page.png');
	Configure::write('BcContents.items.Blog.BlogContent.icon', 'admin/icon_blog.png');
	Configure::write('BcContents.items.Mail.MailContent.icon', 'admin/icon_mail.png');
}
