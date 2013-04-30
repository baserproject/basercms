<?php
/* SVN FILE: $Id$ */
/**
 * baserCMS共通関数
 *
 * baser/config/bootstrapより呼び出される
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
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
		if($baseUrl) {
			if(!preg_match('/\/$/', $baseUrl)) {
				$baseUrl .= '/';
			}
		}else {
			if(!empty($_SERVER['QUERY_STRING'])) {
				// $_GET['url'] からURLを取得する場合、Controller::requestAction では、
				// $_GET['url'] をリクエストしたアクションのURLで書き換えてしまい
				// ベースとなるURLが取得できないので、$_SERVER['QUERY_STRING'] を利用
				$url = '';
				if(preg_match('/url=([^&]+)(&|$)/', $_SERVER['QUERY_STRING'], $maches)) {
					$url = $maches[1];
				}
				if($url) {
					$requestUri = '/';
					if(!empty($_SERVER['REQUEST_URI'])) {
						$requestUri = urldecode($_SERVER['REQUEST_URI']);
					}
					if(strpos($requestUri, '?') !== false) {
						list($requestUri) = explode('?', $requestUri);
					}
					$baseUrl = str_replace($url, '', $requestUri);
				}
				
			} else {
				// /index の場合、$_SERVER['QUERY_STRING'] が入ってこない為
				$requestUri = '/';
				if(!empty($_SERVER['REQUEST_URI'])) {
					$requestUri = $_SERVER['REQUEST_URI'];
				}
				$baseUrl = preg_replace("/index$/", '', $requestUri);
			}
		}
		if(!$baseUrl){
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

		if(empty($_SERVER['SCRIPT_NAME'])) {
			return '';
		}		
		
		if(strpos($_SERVER['SCRIPT_NAME'],'.php') === false){
			// さくらの場合、/index を呼びだすと、拡張子が付加されない
			$scriptName = $_SERVER['SCRIPT_NAME'] . '.php';
		}else{
			$scriptName = $_SERVER['SCRIPT_NAME'];
		}
		$path = explode('/', $scriptName);
		krsort($path);
		// WINDOWS環境の場合、SCRIPT_NAMEのDIRECTORY_SEPARATORがスラッシュの場合があるので
		// スラッシュに一旦置換してスラッシュベースで解析
		$docRoot = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
		foreach($path as $value) {
			$reg = "/\/".$value."$/";
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
		if(preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)([\sa-z\-]+|\.[0-9]+|)([\sa-z\-]+|\.[0-9]+|)/is", $version, $maches)) {
			if(isset($maches[4]) && preg_match('/^\.[0-9]+$/', $maches[4])) {
				if(isset($maches[5]) && preg_match('/^[\sa-z\-]+$/', $maches[5])) {
					return false;
				}
				$maches[4] = str_replace('.', '', $maches[4]);
			} elseif(isset($maches[4]) && preg_match('/^[\sa-z\-]+$/', $maches[4])) {
				return false;
			} else {
				$maches[4] = 0;
			}
			return $maches[1]*1000000000 + $maches[2]*1000000 + $maches[3]*1000 + $maches[4];
		}else {
			return 0;
		}
	}
/**
 * 拡張子を取得する
 * @param	string	mimeタイプ
 * @return	string	拡張子
 * @access	public
 */
	function decodeContent($content,$fileName=null) {

		$contentsMaping=array(
				"image/gif" => "gif",
				"image/jpeg" => "jpg",
				"image/pjpeg" => "jpg",
				"image/x-png" => "png",
				"image/jpg" => "jpg",
				"image/png" => "png",
				"application/x-shockwave-flash" => "swf",
				/*"application/pdf" => "pdf",*/ // TODO windows で ai ファイルをアップロードをした場合、headerがpdfとして出力されるのでコメントアウト
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
		} elseif($fileName) {
			$info = pathinfo($fileName);
			if(!empty($info['extension'])) {
				return $info['extension'];
			}else {
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
		if(strpos($url, '?') !== false) {
			list($url) = explode('?', $url);
		}
		
		return preg_replace('/^'.$agentAlias.'\//','',$url);
		
	}
/**
 * 環境変数よりURLを取得する
 * 
 * スマートURLオフ＆bootstrapのタイミングでは、$_GET['url']が取得できてない為、それをカバーする為に利用する
 * ＊ 先頭のスラッシュは除外する
 * ＊ baseUrlは除外する
 * 
 * TODO QUERY_STRING ではなく、全て REQUEST_URI で判定してよいのでは？
 */
	function getUrlFromEnv() {
		
		if(!empty($_GET['url'])) {
			return preg_replace('/^\//', '', $_GET['url']);
		}
		
		if(!isset($_SERVER['REQUEST_URI'])) {
			return;
		} else {
			$requestUri = $_SERVER['REQUEST_URI'];
		}

		$appBaseUrl = Configure::read('App.baseUrl');
		$parameter = '';
		
		if($appBaseUrl) {
			
			$base = dirname($appBaseUrl);
			if(strpos($requestUri, $appBaseUrl) !== false) {
				$parameter = str_replace($appBaseUrl, '', $requestUri);
			}else {
				// トップページ
				$parameter = str_replace($base.'/', '', $requestUri);
			}
			
		}else {
			
			$parameter = '';
			if(isset($_SERVER['QUERY_STRING'])) {
				$query = $_SERVER['QUERY_STRING'];
			}
			if(!empty($query)){
				if(strpos($query, '&')){
					$queries = explode('&',$query);
					foreach($queries as $_query) {
						if(strpos($_query, '=')){
							list($key,$value) = explode('=',$_query);
							if($key=='url'){
								$parameter = $value;
								break;
							}
						}
					}
				}else{
					if(strpos($query, '=')){
						list($key,$value) = explode('=',$query);
						if($key=='url'){
							$parameter = $value;
						}
					}
				}

			}elseif (preg_match('/^'.str_replace('/', '\/', baseUrl()).'/is', $requestUri)){
				$parameter = preg_replace('/^'.str_replace('/', '\/', baseUrl()).'/is', '', $requestUri);
			} else {
				$parameter = $requestUri;
			}
		}
		$parameter = preg_replace('/^\//','',$parameter);
		
		return $parameter;

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
	function clearViewCache($url=null,$ext='.php') {

		$url = preg_replace('/^\/mobile\//is', '/m/', $url);
		if ($url == '/' || $url == '/index' || $url == '/index.html' || $url == '/m/' || $url == '/m/index' || $url == '/m/index.html') {
			$homes = array('','index','index_html');
			foreach($homes as $home){
				if(preg_match('/^\/m/is',$url)){
					if($home){
						$home = 'm_'.$home;
					}else{
						$home = 'm';
					}
				} elseif (preg_match('/^\/s/is', $url)) {
					if($home){
						$home = 's_'.$home;
					}else{
						$home = 's';
					}
				}
				$baseUrl = baseUrl();
				if($baseUrl) {
					$baseUrl = str_replace(array('/', '.'), '_', $baseUrl);
					$baseUrl = preg_replace('/^_/', '', $baseUrl);
					$baseUrl = preg_replace('/_$/', '', $baseUrl);
					if($home){
						$home = $baseUrl.$home;
					}else{
						$home = $baseUrl;
					}
				}elseif(!$home){
					$home = 'home';
				}
				clearCache($home);
			}
		}elseif($url) {
			if(preg_match('/\/index$/', $url)) {
				clearCache(strtolower(Inflector::slug($url)), 'views', $ext);
				$url = preg_replace('/\/index$/', '', $url);
				clearCache(strtolower(Inflector::slug($url)), 'views', $ext);
			} else {
				clearCache(strtolower(Inflector::slug($url)), 'views', $ext);
			}
		}else {
			App::import('Core','Folder');
			$folder = new Folder(CACHE.'views'.DS);
			$files = $folder->read(true,true);
			foreach($files[1] as $file) {
				if($file != 'empty') {
					@unlink(CACHE.'views'.DS.$file);
				}
			}
		}

	}
/**
 * データキャッシュを削除する
 */
	function clearDataCache() {
		
		App::import('Core','Folder');
		$folder = new Folder(CACHE.'datas'.DS);

		$files = $folder->read(true,true,true);
		foreach($files[1] as $file) {
			@unlink($file);
		}
		$Folder = new Folder();
		foreach($files[0] as $folder) {
			$Folder->delete($folder);
		}
		
	}
/**
 * キャッシュファイルを全て削除する
 */
	function clearAllCache() {

		Cache::clear(false,'_cake_core_');
		Cache::clear(false,'_cake_model_');
		Cache::clear(false,'_cake_env_');
		// viewキャッシュ削除
		clearCache();
		// dataキャッシュ削除
		clearDataCache();
		
	}
/**
 * baserCMSのインストールが完了しているかチェックする
 * @return	boolean
 */
	function isInstalled () {
		
		if(getDbConfig() && file_exists(CONFIGS.'install.php')){
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
		
		if(file_exists(CONFIGS.'database.php')) {
			require_once CONFIGS.'database.php';
			$dbConfig = new DATABASE_CONFIG();
			if(!empty($dbConfig->{$name}['driver'])){
				return $dbConfig->{$name};
			}
		}
		return false;
		
	}
/**
 * 必要な一時フォルダが存在するかチェックし、
 * なければ生成する
 */
	function checkTmpFolders(){

		if(!is_writable(TMP)){
			return;
		}
		App::import('Core','Folder');
		$folder = new Folder();
		$folder->create(TMP.'logs',0777);
		$folder->create(TMP.'sessions',0777);
		$folder->create(TMP.'schemas',0777);
		$folder->create(TMP.'schemas'.DS.'baser', 0777);
		$folder->create(TMP.'schemas'.DS.'plugin', 0777);
		$folder->create(CACHE, 0777);
		$folder->create(CACHE.'models',0777);
		$folder->create(CACHE.'persistent',0777);
		$folder->create(CACHE.'views',0777);
		$folder->create(CACHE.'datas',0777);
		$folder->create(CACHE.'environment',0777);

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
		if(is_array($files[1])) {
			foreach($files[1] as $file) {
				if($file != 'empty') {
					if(!@unlink($file)) {
						$result = false;
					}
				}
			}
		}
		if(is_array($files[0])) {
			foreach($files[0] as $file) {
				if(!emptyFolder($file)) {
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
		}else {
			$SiteConfig = ClassRegistry::init('SiteConfig');
		}
		$siteConfig = $SiteConfig->findExpanded();
		$theme = $siteConfig['theme'];
		if($theme) {
			return WWW_ROOT.'themed'.DS.$theme.DS;
		}else {
			return VIEWS;
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
	function fgetcsvReg (&$handle, $length = null, $d = ',', $e = '"') {
		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		$eof = false;
		while (($eof != true)and(!feof($handle))) {
			$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
			if ($itemcnt % 2 == 0) $eof = true;
		}
		$_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
		$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++) {
			$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
			$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
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
		return topLevelUrl(false).$url;
	}
/**
 * サイトのトップレベルのURLを取得する
 *
 * @param	boolean	$lastSlash
 * @return	string
  */
	function topLevelUrl($lastSlash = true) {
		$protocol = 'http://';
		if(!empty($_SERVER['HTTPS'])) {
			$protocol = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];
		$url = $protocol.$host;
		if($lastSlash) {
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
		return topLevelUrl(false).$baseUrl;
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
			if(is_array($v)) {
				if(isset($a[$k])) {
					$a[$k] = amr($a[$k], $v);
					continue;
				}
			}
			if(!is_array($a)) {
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
 */
	function loadPluginConfig($name) {

		if(strpos($name, '.') === false) {
			return false;
		}
		list($plugin, $file) = explode('.', $name);
		$plugin = Inflector::underscore($plugin);
		$pluginPaths = array(
			APP.'plugins'.DS,
			BASER_PLUGINS
		);
		$config = null;
		foreach($pluginPaths as $pluginPath) {
			$configPath = $pluginPath.$plugin.DS.'config'.DS.$file.'.php';
			if(file_exists($configPath)) {
				include $configPath;
			}
		}

		if($config) {
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
		if(Configure::read('BcRequest.agent') == 'mobile' && Configure::read('BcAgent.mobile.sessionId') && (!ini_get('session.use_trans_sid') || $force)) {
			if(is_array($url)) {
				$url["?"][session_name()] = session_id();
			} else {
				if(strpos($url, '?') !== false) {
					$args = array();
					$_url = explode('?', $url);
					if(!empty($_url[1])) {
						if(strpos($_url[1], '&') !== false) {
							$aryUrl = explode('&', $_url[1]);
							foreach($aryUrl as $pass) {
								if(strpos($pass, '=') !== false) {
									list($key, $value) = explode('=', $pass);
									$args[$key] = $value;
								}
							}
						} else {
							if(strpos($_url[1], '=') !== false) {
								list($key, $value) = explode('=', $_url[1]);
								$args[$key] = $value;
							}
						}
					}
					$args[session_name()] = session_id();
					$pass = '';
					foreach($args as $key => $value) {
						if($pass) {
							$pass .= '&';
						}
						$pass .= $key.'='.$value;
					}
					$url = $_url[0] . '?' . $pass;
				} else {
					$url .= '?'.session_name().'='.session_id();
				}
			}
		}
		return $url;
		
	}
/**
 * 利用可能なプラグインのリストを取得する
 * 
 * PluginHookBehavior::setup() で、プラグインリストを参照できるように、
 * ClassRegistry::removeObject('Plugin'); で一旦 Plugin オブジェクトを削除
 * エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
 * 
 * @return array
 */
	function getEnablePlugins() {
		
		$enablePlugins = array();
		if(!Configure::read('Cache.disable')) {
			$enablePlugins = Cache::read('enable_plugins', '_cake_env_');
		}
		if(!$enablePlugins) {
			$db =& ConnectionManager::getDataSource('baser');
			$sources = $db->listSources();
			$pluginTable = $db->config['prefix'] . 'plugins';
			$enablePlugins = array();
			if (!is_array($sources) || in_array(strtolower($pluginTable), array_map('strtolower', $sources))) {
				$Plugin = ClassRegistry::init('Plugin');
				$plugins = $Plugin->find('all', array('fields' => array('Plugin.name'), 'conditions' => array('Plugin.status' => true)));
				ClassRegistry::removeObject('Plugin');
				if($plugins) {
					$enablePlugins = Set::extract('/Plugin/name',$plugins);
					
					if(!Configure::read('Cache.disable')) {
						Cache::write('enable_plugins', $enablePlugins, '_cake_env_');
					}
				}
			}
		}
		return $enablePlugins;
		
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
		if(!$plugin || in_array($plugin, $corePlugins)) {
			$path = BASER.'VERSION.txt';
		} else {
			$appPath = APP.'plugins'.DS.$plugin.DS.'VERSION.txt';
			$baserPath = BASER_PLUGINS.$plugin.DS.'VERSION.txt';
			if(file_exists($appPath)) {
				$path = $appPath;
			}elseif(file_exists($baserPath)) {
				$path = $baserPath;
			} else {
				return false;
			}
		}

		App::import('File');
		$versionFile = new File($path);
		$versionData = $versionFile->read();
		$aryVersionData = split("\n",$versionData);
		if(!empty($aryVersionData[0])) {
			return trim($aryVersionData[0]);
		}else {
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

		App::import('Core', 'Email');
		App::import('Component', 'BcEmail', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'controllers'.DS.'components'.DS.'bc_email.php'));
		$BcEmail = new BcEmailComponent();
		if(!empty($bcSite['mail_encode'])) {
			$encode = $bcSite['mail_encode'];
		} else {
			$encode = 'ISO-2022-JP';
		}
		$BcEmail->charset = $encode;
		$BcEmail->sendAs = 'text';
		$BcEmail->lineLength=105;
		if(!empty($bcSite['smtp_host'])) {
			$BcEmail->delivery = 'smtp';
			$BcEmail->smtpOptions = array('host'	=>$bcSite['smtp_host'],
					'port'		=> 25,
					'timeout'	=> 30,
					'username'	=> ($bcSite['smtp_user'])?$bcSite['smtp_user']:null,
					'password'	=> ($bcSite['smtp_password'])?$bcSite['smtp_password']:null);
		} else {
			$BcEmail->delivery = "mail";
		}
		$BcEmail->to = $bcSite['email'];
		$BcEmail->subject = 'baserCMSアップデート';
		$BcEmail->from = $bcSite['name'].' <'.$bcSite['email'].'>';
		$message = array();
		$message[] = '下記のURLよりbaserCMSのアップデートを完了してください。';
		$message[] = topLevelUrl(false).baseUrl().'updaters/index/'.$bcSite['update_id'];
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
		$debug = Configure::read();
		if($debug < 1) {
			Configure::write('debug', 1);
		}
		$calledFrom = debug_backtrace();
		echo '<strong style="font-size:10px">' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
		echo '<span style="font-size:10px"> (line <strong>' . $calledFrom[0]['line'] . '</strong>)</span>';
		debug($var, true, false);
		if($debug < 1) {
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
		return $db->config['driver'];
		
	}