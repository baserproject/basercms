<?php

/**
 * baserCMS共通関数
 *
 * baser/config/bootstrapより呼び出される
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('EmailComponent', 'Controller/Component');
App::uses('BcEmailComponent', 'Controller/Component');

/**
 * WEBサイトのベースとなるURLを取得する
 * 
 * コントローラーが初期化される前など {$this->base} が利用できない場合に利用する
 * / | /index.php/ | /subdir/ | /subdir/index.php/
 * 
 * ※ プログラムフォルダ内の画像やCSSの読み込み時もbootstrap.php で呼び出されるのでサーバーキャッシュは利用しない
 * 
 * @return string ベースURL
 */
function baseUrl() {

	$baseUrl = Configure::read('App.baseUrl');
	if ($baseUrl) {
		if (!preg_match('/\/$/', $baseUrl)) {
			$baseUrl .= '/';
		}
	} else {
		$script = $_SERVER['SCRIPT_FILENAME'];
		$script = str_replace(docRoot(), '', $script);
		if (BC_DEPLOY_PATTERN == 1) {
			$baseUrl = preg_replace('/' . preg_quote('app' . DS . 'webroot' . DS . 'index.php', '/') . '/', '', $script);
			$baseUrl = preg_replace('/' . preg_quote('app' . DS . 'webroot' . DS . 'test.php', '/') . '/', '', $baseUrl);
			// ↓ Windows Azure 対策 SCRIPT_FILENAMEに期待した値が入ってこない為
			$baseUrl = preg_replace('/index\.php/', '', $baseUrl);
		} elseif (BC_DEPLOY_PATTERN == 2) {
			$baseUrl = preg_replace('/index\.php/', '', $script);
		}
		$baseUrl = preg_replace("/index$/", '', $baseUrl);
	}

	$baseUrl = str_replace(DS, '/', $baseUrl);
	if (!$baseUrl) {
		$baseUrl = '/';
	}
	return $baseUrl;
	
}

/**
 * ドキュメントルートを取得する
 *
 * サブドメインの場合など、$_SERVER['DOCUMENT_ROOT'] が正常に取得できない場合に利用する
 * UserDir に対応
 *
 * @return string   ドキュメントルートの絶対パス
 */
function docRoot() {

	if (empty($_SERVER['SCRIPT_NAME'])) {
		return '';
	}

	if (strpos($_SERVER['SCRIPT_NAME'], '.php') === false) {
		// さくらの場合、/index を呼びだすと、拡張子が付加されない
		$scriptName = $_SERVER['SCRIPT_NAME'] . '.php';
	} else {
		$scriptName = $_SERVER['SCRIPT_NAME'];
	}
	$path = explode('/', $scriptName);
	krsort($path);
	// WINDOWS環境の場合、SCRIPT_NAMEのDIRECTORY_SEPARATORがスラッシュの場合があるので
	// スラッシュに一旦置換してスラッシュベースで解析
	$docRoot = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
	foreach ($path as $value) {
		$reg = "/\/" . $value . "$/";
		$docRoot = preg_replace($reg, '', $docRoot);
	}
	return str_replace('/', DS, $docRoot);
}

/**
 * リビジョンを取得する
 * @param string    baserCMS形式のバージョン表記　（例）baserCMS 1.5.3.1600 beta
 * @return string   リビジョン番号
 */
function revision($version) {
	return preg_replace("/baserCMS [0-9]+?\.[0-9]+?\.[0-9]+?\.([0-9]*)[\sa-z]*/is", "$1", $version);
}

/**
 * バージョンを特定する一意の数値を取得する
 * ２つ目以降のバージョン番号は３桁として結合
 * 1.5.9 => 1005009
 * ※ ２つ目以降のバージョン番号は999までとする
 * β版の場合はfalseを返す
 * 
 * @param mixed $version Or false
 */
function verpoint($version) {
	$version = str_replace('baserCMS ', '', $version);
	if (preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)([\sa-z\-]+|\.[0-9]+|)([\sa-z\-]+|\.[0-9]+|)/is", $version, $maches)) {
		if (isset($maches[4]) && preg_match('/^\.[0-9]+$/', $maches[4])) {
			if (isset($maches[5]) && preg_match('/^[\sa-z\-]+$/', $maches[5])) {
				return false;
			}
			$maches[4] = str_replace('.', '', $maches[4]);
		} elseif (isset($maches[4]) && preg_match('/^[\sa-z\-]+$/', $maches[4])) {
			return false;
		} else {
			$maches[4] = 0;
		}
		return $maches[1] * 1000000000 + $maches[2] * 1000000 + $maches[3] * 1000 + $maches[4];
	} else {
		return 0;
	}
}

/**
 * 拡張子を取得する
 * @param	string	mimeタイプ
 * @return	string	拡張子
 * @access	public
 */
function decodeContent($content, $fileName = null) {

	$contentsMaping = array(
		"image/gif" => "gif",
		"image/jpeg" => "jpg",
		"image/pjpeg" => "jpg",
		"image/x-png" => "png",
		"image/jpg" => "jpg",
		"image/png" => "png",
		"application/x-shockwave-flash" => "swf",
		/* "application/pdf" => "pdf", */ // TODO windows で ai ファイルをアップロードをした場合、headerがpdfとして出力されるのでコメントアウト
		"application/pgp-signature" => "sig",
		"application/futuresplash" => "spl",
		"application/msword" => "doc",
		"application/postscript" => "ai",
		"application/x-bittorrent" => "torrent",
		"application/x-dvi" => "dvi",
		"application/x-gzip" => "gz",
		"application/x-ns-proxy-autoconfig" => "pac",
		"application/x-shockwave-flash" => "swf",
		"application/x-tgz" => "tar.gz",
		"application/x-tar" => "tar",
		"application/zip" => "zip",
		"audio/mpeg" => "mp3",
		"audio/x-mpegurl" => "m3u",
		"audio/x-ms-wma" => "wma",
		"audio/x-ms-wax" => "wax",
		"audio/x-wav" => "wav",
		"image/x-xbitmap" => "xbm",
		"image/x-xpixmap" => "xpm",
		"image/x-xwindowdump" => "xwd",
		"text/css" => "css",
		"text/html" => "html",
		"text/javascript" => "js",
		"text/plain" => "txt",
		"text/xml" => "xml",
		"video/mpeg" => "mpeg",
		"video/quicktime" => "mov",
		"video/x-msvideo" => "avi",
		"video/x-ms-asf" => "asf",
		"video/x-ms-wmv" => "wmv"
	);

	if (isset($contentsMaping[$content])) {
		return $contentsMaping[$content];
	} elseif ($fileName) {
		$info = pathinfo($fileName);
		if (!empty($info['extension'])) {
			return $info['extension'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * 環境変数よりURLパラメータを取得する
 * 
 * ＊ モバイルプレフィックスは除外する
 * ＊ GETパラメーターは除外する
 * 
 * 《注意》
 * bootstrap 実行後でのみ利用可 
 */
function getUrlParamFromEnv() {

	$agentAlias = Configure::read('BcRequest.agentAlias');
	$url = getUrlFromEnv();

	if (strpos($url, '?') !== false) {
		list($url) = explode('?', $url);
	}

	return preg_replace('/^' . $agentAlias . '\//', '', $url);
}

/**
 * 環境変数よりURLを取得する
 * 
 * スマートURLオフ＆bootstrapのタイミングでは、$_GET['url']が取得できてない為、それをカバーする為に利用する
 * ＊ 先頭のスラッシュは除外する
 * ＊ baseUrlは除外する
 *  
 */
function getUrlFromEnv() {

	if (!empty($_GET['url'])) {
		return preg_replace('/^\//', '', $_GET['url']);
	}

	if (!isset($_SERVER['REQUEST_URI'])) {
		return;
	} else {
		$requestUri = $_SERVER['REQUEST_URI'];
	}

	$appBaseUrl = Configure::read('App.baseUrl');
	$parameter = '';

	if ($appBaseUrl) {

		$base = dirname($appBaseUrl);
		if (strpos($requestUri, $appBaseUrl) !== false) {
			$parameter = str_replace($appBaseUrl, '', $requestUri);
		} else {
			// トップページ
			$parameter = str_replace($base . '/', '', $requestUri);
		}
	} else {

		if (strpos($requestUri, '?')) {
			$aryRequestUri = explode('?', $requestUri);
			$requestUri = $aryRequestUri[0];
		}
		if (preg_match('/^' . str_replace('/', '\/', baseUrl()) . '/is', $requestUri)) {
			$parameter = preg_replace('/^' . str_replace('/', '\/', baseUrl()) . '/is', '', $requestUri);
		} else {
			$parameter = $requestUri;
		}
	}

	return preg_replace('/^\//', '', $parameter);
}

/**
 * モバイルプレフィックスは除外したURLを取得する
 * 
 * @param CakeRequest $Request
 * @return type
 */
function getPureUrl($Request) {

	if (!$Request) {
		$Request = new CakeRequest();
	}
	$agentAlias = Configure::read('BcRequest.agentAlias');
	$url = $Request->url;
	if ($url === false) {
		$url = '/';
	}
	if (strpos($url, '?') !== false) {
		list($url) = explode('?', $url);
	}
	return preg_replace('/^' . $agentAlias . '\//', '', $url);
}

/**
 * Viewキャッシュを削除する
 * URLを指定しない場合は全てのViewキャッシュを削除する
 * 全て削除する場合、標準の関数clearCacheだとemptyファイルまで削除されてしまい、
 * 開発時に不便なのでFolderクラスで削除
 *
 * @param	$url
 * @return	void
 * @access	public
 */
function clearViewCache($url = null, $ext = '.php') {

	$url = preg_replace('/^\/mobile\//is', '/m/', $url);
	if ($url == '/' || $url == '/index' || $url == '/index.html' || $url == '/m/' || $url == '/m/index' || $url == '/m/index.html') {
		$homes = array('', 'index', 'index_html');
		foreach ($homes as $home) {
			if (preg_match('/^\/m/is', $url)) {
				if ($home) {
					$home = 'm_' . $home;
				} else {
					$home = 'm';
				}
			} elseif (preg_match('/^\/s/is', $url)) {
				if ($home) {
					$home = 's_' . $home;
				} else {
					$home = 's';
				}
			}
			$baseUrl = baseUrl();
			if ($baseUrl) {
				$baseUrl = str_replace(array('/', '.'), '_', $baseUrl);
				$baseUrl = preg_replace('/^_/', '', $baseUrl);
				$baseUrl = preg_replace('/_$/', '', $baseUrl);
				if ($home) {
					$home = $baseUrl . $home;
				} else {
					$home = $baseUrl;
				}
			} elseif (!$home) {
				$home = 'home';
			}
			clearCache($home);
		}
	} elseif ($url) {
		if (preg_match('/\/index$/', $url)) {
			clearCache(strtolower(Inflector::slug($url)), 'views', $ext);
			$url = preg_replace('/\/index$/', '', $url);
			clearCache(strtolower(Inflector::slug($url)), 'views', $ext);
		} else {
			clearCache(strtolower(Inflector::slug($url)), 'views', $ext);
		}
	} else {
		$folder = new Folder(CACHE . 'views' . DS);
		$files = $folder->read(true, true);
		foreach ($files[1] as $file) {
			if ($file != 'empty') {
				@unlink(CACHE . 'views' . DS . $file);
			}
		}
	}
}

/**
 * データキャッシュを削除する
 */
function clearDataCache() {

	App::import('Core', 'Folder');
	$folder = new Folder(CACHE . 'datas' . DS);

	$files = $folder->read(true, true, true);
	foreach ($files[1] as $file) {
		@unlink($file);
	}
	$Folder = new Folder();
	foreach ($files[0] as $folder) {
		$Folder->delete($folder);
	}
}

/**
 * キャッシュファイルを全て削除する
 */
function clearAllCache() {

	Cache::clear(false, '_cake_core_');
	Cache::clear(false, '_cake_model_');
	Cache::clear(false, '_cake_env_');
	// viewキャッシュ削除
	clearCache();
	// dataキャッシュ削除
	clearDataCache();
}

/**
 * baserCMSのインストールが完了しているかチェックする
 * @return	boolean
 */
function isInstalled() {

	if (getDbConfig() && file_exists(APP . 'Config' . DS . 'install.php')) {
		return true;
	}
	return false;
}

/**
 * DBセッティングが存在するかチェックする
 * 
 * @param string $name
 * @return mixed DatabaseConfig Or false 
 */
function getDbConfig($name = 'baser') {

	if (file_exists(APP . 'Config' . DS . 'database.php')) {
		require_once APP . 'Config' . DS . 'database.php';
		$dbConfig = new DATABASE_CONFIG();
		if (!empty($dbConfig->{$name}['datasource'])) {
			return $dbConfig->{$name};
		}
	}
	return false;
}

/**
 * 必要な一時フォルダが存在するかチェックし、
 * なければ生成する
 */
function checkTmpFolders() {

	if (!is_writable(TMP)) {
		return;
	}
	$folder = new Folder();
	$folder->create(TMP . 'logs', 0777);
	$folder->create(TMP . 'sessions', 0777);
	$folder->create(TMP . 'schemas', 0777);
	$folder->create(TMP . 'schemas' . DS . 'baser', 0777);
	$folder->create(TMP . 'schemas' . DS . 'plugin', 0777);
	$folder->create(CACHE, 0777);
	$folder->create(CACHE . 'models', 0777);
	$folder->create(CACHE . 'persistent', 0777);
	$folder->create(CACHE . 'views', 0777);
	$folder->create(CACHE . 'datas', 0777);
	$folder->create(CACHE . 'environment', 0777);
}

/**
 * フォルダの中をフォルダを残して空にする
 *
 * @param	string	$path
 * @return	boolean
 */
function emptyFolder($path) {

	$result = true;
	$Folder = new Folder($path);
	$files = $Folder->read(true, true, true);
	if (is_array($files[1])) {
		foreach ($files[1] as $file) {
			if ($file != 'empty') {
				if (!@unlink($file)) {
					$result = false;
				}
			}
		}
	}
	if (is_array($files[0])) {
		foreach ($files[0] as $file) {
			if (!emptyFolder($file)) {
				$result = false;
			}
		}
	}
	return $result;
}

/**
 * 現在のビューディレクトリのパスを取得する
 *
 * @return string
 */
function getViewPath() {

	if (ClassRegistry::isKeySet('SiteConfig')) {
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
	} else {
		$SiteConfig = ClassRegistry::init('SiteConfig');
	}
	$siteConfig = $SiteConfig->findExpanded();
	$theme = $siteConfig['theme'];
	if ($theme) {
		return WWW_ROOT . 'theme' . DS . $theme . DS;
	} else {
		return APP . 'View' . DS;
	}
}

/**
 * ファイルポインタから行を取得し、CSVフィールドを処理する
 *
 * @param	stream	handle
 * @param	int		length
 * @param	string	delimiter
 * @param 	string	enclosure
 * @return	mixed	ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
 */
function fgetcsvReg(&$handle, $length = null, $d = ',', $e = '"') {
	$d = preg_quote($d);
	$e = preg_quote($e);
	$_line = "";
	$eof = false;
	while (($eof != true) and (!feof($handle))) {
		$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
		$itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
		if ($itemcnt % 2 == 0)
			$eof = true;
	}
	$_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
	$_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
	preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
	$_csv_data = $_csv_matches[1];
	for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
		$_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1', $_csv_data[$_csv_i]);
		$_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
	}
	return empty($_line) ? false : $_csv_data;
}

/**
 * httpからのフルURLを取得する
 *
 * @param	mixed	$url
 * @return	string
 */
function fullUrl($url) {
	$url = Router::url($url);
	return topLevelUrl(false) . $url;
}

/**
 * サイトのトップレベルのURLを取得する
 *
 * @param	boolean	$lastSlash
 * @return	string
 */
function topLevelUrl($lastSlash = true) {
	if (isConsole()) {
		return false;
	}
	$protocol = 'http://';
	if (!empty($_SERVER['HTTPS'])) {
		$protocol = 'https://';
	}
	$host = $_SERVER['HTTP_HOST'];
	$url = $protocol . $host;
	if ($lastSlash) {
		$url .= '/';
	}
	return $url;
}

/**
 * サイトの設置URLを取得する
 *
 * index.phpは含まない
 *
 * @return	string
 */
function siteUrl() {
	$baseUrl = preg_replace('/index\.php\/$/', '', baseUrl());
	return topLevelUrl(false) . $baseUrl;
}

/**
 * 配列を再帰的に上書きする
 * 二つまで
 * @param	array	$a
 * @param	array	$b
 * @return	array
 */
function amr($a, $b) {

	foreach ($b as $k => $v) {
		if (is_array($v)) {
			if (isset($a[$k])) {
				$a[$k] = amr($a[$k], $v);
				continue;
			}
		}
		if (!is_array($a)) {
			$a = array($a);
		}
		$a[$k] = $v;
	}
	return $a;
}

/**
 * プラグインのコンフィグファイルを読み込む
 *
 * @param string $name
 * @return boolean
 * @deprecated since version 3.0.2
 */
function loadPluginConfig($name) {

	if (strpos($name, '.') === false) {
		return false;
	}
	list($plugin, $file) = explode('.', $name);
	$pluginPaths = App::path('Plugin');
	$config = null;
	foreach ($pluginPaths as $pluginPath) {
		$configPath = $pluginPath . $plugin . DS . 'Config' . DS . $file . '.php';
		if (file_exists($configPath)) {
			include $configPath;
		}
	}

	if ($config) {
		return Configure::write($config);
	} else {
		return false;
	}
}

/**
 * URLにセッションIDを付加する
 * 既に付加されている場合は重複しない
 * 
 * @param mixed $url
 * @return mixed
 */
function addSessionId($url, $force = false) {

	// use_trans_sid が有効になっている場合、２重で付加されてしまう
	if (Configure::read('BcRequest.agent') == 'mobile' && Configure::read('BcAgent.mobile.sessionId') && (!ini_get('session.use_trans_sid') || $force)) {
		if (is_array($url)) {
			$url["?"][session_name()] = session_id();
		} else {
			if (strpos($url, '?') !== false) {
				$args = array();
				$_url = explode('?', $url);
				if (!empty($_url[1])) {
					if (strpos($_url[1], '&') !== false) {
						$aryUrl = explode('&', $_url[1]);
						foreach ($aryUrl as $pass) {
							if (strpos($pass, '=') !== false) {
								list($key, $value) = explode('=', $pass);
								$args[$key] = $value;
							}
						}
					} else {
						if (strpos($_url[1], '=') !== false) {
							list($key, $value) = explode('=', $_url[1]);
							$args[$key] = $value;
						}
					}
				}
				$args[session_name()] = session_id();
				$pass = '';
				foreach ($args as $key => $value) {
					if ($pass) {
						$pass .= '&';
					}
					$pass .= $key . '=' . $value;
				}
				$url = $_url[0] . '?' . $pass;
			} else {
				$url .= '?' . session_name() . '=' . session_id();
			}
		}
	}
	return $url;
}

/**
 * 利用可能なプラグインのリストを取得する
 * 
 * ClassRegistry::removeObject('Plugin'); で一旦 Plugin オブジェクトを削除
 * エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
 * 
 * @return array
 */
function getEnablePlugins() {

	$enablePlugins = array();
	if (!Configure::read('Cache.disable') && Configure::read('debug') == 0) {
		$enablePlugins = Cache::read('enable_plugins', '_cake_env_');
	}
	if (!$enablePlugins) {
		$Plugin = ClassRegistry::init('Plugin');   // ConnectionManager の前に呼出さないとエラーとなる
		$db = ConnectionManager::getDataSource('baser');
		$sources = $db->listSources();
		$pluginTable = $db->config['prefix'] . 'plugins';
		$enablePlugins = array();
		if (!is_array($sources) || in_array(strtolower($pluginTable), array_map('strtolower', $sources))) {
			$plugins = $Plugin->find('all', array('fields' => array('Plugin.name'), 'conditions' => array('Plugin.status' => true), 'order' => 'Plugin.priority'));
			ClassRegistry::removeObject('Plugin');
			if ($plugins) {
				$enablePlugins = Hash::extract($plugins, '{n}.Plugin.name');

				if (!Configure::read('Cache.disable')) {
					Cache::write('enable_plugins', $plugins, '_cake_env_');
				}
			}
		}
	} else {
		$plugins = $enablePlugins;
	}
	return $plugins;
}

/**
 * サイト基本設定をConfigureへ読み込む
 * 
 * @return void
 */
function loadSiteConfig() {

	$SiteConfig = ClassRegistry::init('SiteConfig');
	Configure::write('BcSite', $SiteConfig->findExpanded());
	ClassRegistry::removeObject('SiteConfig');
}

/**
 * バージョンを取得する
 * 
 * @return string Or false
 */
function getVersion($plugin = '') {

	$corePlugins = Configure::read('BcApp.corePlugins');
	if (!$plugin || in_array($plugin, $corePlugins)) {
		$path = BASER . 'VERSION.txt';
	} else {
		$paths = App::path('Plugin');
		$exists = false;
		foreach($paths as $path) {
			$path .=  $plugin . DS . 'VERSION.txt';
			if (file_exists($path)) {
				$exists = true;
				break;
			}
		}
		if(!$exists) {
			return false;
		}
	}

	App::uses('File', 'Utility');
	$versionFile = new File($path);
	$versionData = $versionFile->read();
	$aryVersionData = explode("\n", $versionData);
	if (!empty($aryVersionData[0])) {
		return trim($aryVersionData[0]);
	} else {
		return false;
	}
}

/**
 * アップデートのURLを記載したメールを送信する 
 */
function sendUpdateMail() {

	$bcSite = Configure::read('BcSite');
	$bcSite['update_id'] = String::uuid();
	$SiteConfig = ClassRegistry::init('SiteConfig');
	$SiteConfig->saveKeyValue(array('SiteConfig' => $bcSite));
	ClassRegistry::removeObject('SiteConfig');

	$BcEmail = new BcEmailComponent();
	if (!empty($bcSite['mail_encode'])) {
		$encode = $bcSite['mail_encode'];
	} else {
		$encode = 'ISO-2022-JP';
	}
	$BcEmail->charset = $encode;
	$BcEmail->sendAs = 'text';
	$BcEmail->lineLength = 105;
	if (!empty($bcSite['smtp_host'])) {
		$BcEmail->delivery = 'smtp';
		$BcEmail->smtpOptions = array('host' => $bcSite['smtp_host'],
			'port' => 25,
			'timeout' => 30,
			'username' => ($bcSite['smtp_user']) ? $bcSite['smtp_user'] : null,
			'password' => ($bcSite['smtp_password']) ? $bcSite['smtp_password'] : null);
	} else {
		$BcEmail->delivery = "mail";
	}
	$BcEmail->to = $bcSite['email'];
	$BcEmail->subject = 'baserCMSアップデート';
	$BcEmail->from = $bcSite['name'] . ' <' . $bcSite['email'] . '>';
	$message = array();
	$message[] = '下記のURLよりbaserCMSのアップデートを完了してください。';
	$message[] = topLevelUrl(false) . baseUrl() . 'updaters/index/' . $bcSite['update_id'];
	$BcEmail->send($message);
}

/**
 * 展開出力
 * 
 * デバッグレベルが 0 の時でも強制的に出力する
 * 
 * @param mixed $var
 * @return void
 */
function p($var) {
	$debug = Configure::read('debug');
	if ($debug < 1) {
		Configure::write('debug', 1);
	}
	$calledFrom = debug_backtrace();
	echo '<strong style="font-size:10px">' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
	echo '<span style="font-size:10px"> (line <strong>' . $calledFrom[0]['line'] . '</strong>)</span>';
	debug($var, true, false);
	if ($debug < 1) {
		Configure::write('debug', $debug);
	}
}

/**
 * データベースのドライバー名を取得する
 * 
 * @param string $dbConfigKeyName
 * @return string 
 */
function getDbDriver($dbConfigKeyName = 'baser') {

	$db = ConnectionManager::getDataSource($dbConfigKeyName);
	return $db->config['datasource'];
}

/**
 * コンソコールから実行されているかチェックする
 * 
 * @return boolean
 */
function isConsole() {
	return preg_match('/Console\/cake\.php$/', $_SERVER['SCRIPT_FILENAME']);
}

/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 *
 * `aa('a','b')`
 *
 * Would return:
 *
 * `array('a'=>'b')`
 *
 * @return array Associative array
 * @link http://book.cakephp.org/view/695/aa
 */
function aa() {
	$args = func_get_args();
	$argc = count($args);
	for ($i = 0; $i < $argc; $i++) {
		if ($i + 1 < $argc) {
			$a[$args[$i]] = $args[$i + 1];
		} else {
			$a[$args[$i]] = null;
		}
		$i++;
	}
	return $a;
}

/**
 * 日本語ファイル名対応版basename
 * 
 * @param string $str
 * @param string $suffix
 * @return type
 */
function mb_basename($str, $suffix=null){
  $tmp = preg_split('/[\/\\\\]/', $str);
  $res = end($tmp);
  if(strlen($suffix)){
    $suffix = preg_quote($suffix);
    $res = preg_replace("/({$suffix})$/u", "", $res);
  }
  return $res;
}

/**
 * プラグインを読み込む
 * 
 * @param string $plugin
 * @return type
 */
function loadPlugin($plugin, $priority) {
	if(CakePlugin::loaded($plugin)) {
		return true;
	}
	try {
		CakePlugin::load($plugin);
	} catch (Exception $e) {
		return false;
	}
	$pluginPath = CakePlugin::path($plugin);
	$config = array(
		'bootstrap' => file_exists($pluginPath . 'Config' . DS . 'bootstrap.php'),
		'routes' => file_exists($pluginPath . 'Config' . DS . 'routes.php')
	);
	CakePlugin::load($plugin, $config);
	if (file_exists($pluginPath . 'Config' . DS . 'setting.php')) {
		Configure::load($plugin . '.setting');
	}
	// プラグインイベント登録
	$eventTargets = array('Controller', 'Model', 'View', 'Helper');
	foreach ($eventTargets as $eventTarget) {
		$eventClass = $plugin . $eventTarget . 'EventListener';
		if (file_exists($pluginPath . 'Event' . DS . $eventClass . '.php')) {
			App::uses($eventClass, $plugin . '.Event');
			$CakeEvent = CakeEventManager::instance();
			$CakeEvent->attach(new $eventClass(), null, array('priority' => $priority));
		}
	}
	return true;
}

/**
 * 後方互換の為の非推奨メッセージを生成する
 * 
 * @param string $target 非推奨の対象
 * @param string $since 非推奨となったバージョン
 * @param string $remove 削除予定のバージョン
 * @param string $note その他特記事項
 * @return string 非推奨メッセージ
 */
function deprecatedMessage($target, $since, $remove = null, $note = null) {

	if(Configure::read('debug') == 0) {
		return;
	}
	$message = $target . 'は、バージョン ' . $since . ' より被推奨となりました。';
	if($remove) {
		$message .= 'バージョン ' . $remove . ' で削除される予定です。';
	}
	if($note) {
		$message .= $note;
	}
	return $message;

}

/**
 * パーセントエンコーディングされないURLセーフなbase64エンコード
 * 
 * base64エンコード時でに出てくる記号 +(プラス) , /(スラッシュ) , =(イコール) 
 * このbase64エンコードした値をさらにURLのパラメータで使うためにURLエンコードすると
 * パーセントエンコーディングされてしまいます。
 * その為、このメソッドではパーセントエンコーディングされないURLセーフな
 * base64エンコードを行います。
 * 
 * @param string $val 対象文字列
 * @return string
 */
function base64UrlsafeEncode($val) {
	$val = base64_encode($val);
	return str_replace(array('+', '/', '='), array('_', '-', '.'), $val);
}
 
/**
 * パーセントエンコーディングされないURLセーフなbase64デコード
 * 
 * @param string $val 対象文字列
 * @return string
 */
function base64UrlsafeDecode($val) {
	$val = str_replace(array('_','-', '.'), array('+', '/', '='), $val);
	return base64_decode($val);
}

