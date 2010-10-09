<?php
/* SVN FILE: $Id$ */
/**
 * BaserCMS共通関数
 *
 * baser/config/bootstrapより呼び出される
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * WEBサイトのベースとなるURLを取得する
 * コントローラーが初期化される前など {$this->base} が利用できない場合に利用する
 * @return string   ベースURL
 */
	function baseUrl() {
		$appBaseUrl = Configure::read('App.baseUrl');
		if($appBaseUrl) {
			$_url = $_SERVER['REQUEST_URI'];
			$baseUrl = $appBaseUrl.'/';
		}else {
			$_baseUrl = str_replace(DS,'/',preg_replace("/^\\".DS."/i",'',str_replace(docRoot(),'',ROOT)));
			if($_baseUrl) {
				$baseUrl = '/'.$_baseUrl.'/';
			}else {
				$baseUrl = '/';
			}
		}
		return $baseUrl;
	}
/**
 * ドキュメントルートを取得する
 * サブドメインの場合など、$_SERVER['DOCUMENT_ROOT'] が正常に取得できない場合に利用する
 * @return string   ドキュメントルートの絶対パス
 */
	function docRoot() {
		if(strpos($_SERVER['SCRIPT_NAME'],'.php') === false){
			// さくらの場合、/index を呼びだすと、拡張子が付加されない
			$scriptName = $_SERVER['SCRIPT_NAME'] . '.php';
		}else{
			$scriptName = $_SERVER['SCRIPT_NAME'];
		}
		$docRoot = str_replace($scriptName,'',$_SERVER['SCRIPT_FILENAME']);
		return str_replace('/', DS, $docRoot);
	}
/**
 * リビジョンを取得する
 * @param string    BaserCMS形式のバージョン表記　（例）BaserCMS 1.5.3.1600 beta
 * @return string   リビジョン番号
 */
	function revision($version) {
		return preg_replace("/BaserCMS [0-9]+?\.[0-9]+?\.[0-9]+?\.([0-9]*)[\sa-z]*/is", "$1", $version);
	}
/**
 * バージョンを特定する一意の数値を取得する
 * ２つ目以降のバージョン番号は３桁として結合
 * 1.5.9 => 1005009
 * ※ ２つ目以降のバージョン番号は999までとする
 * @param string $version
 */
	function verpoint($version) {
		if(preg_match("/BaserCMS ([0-9]+)\.([0-9]+)\.([0-9]+)[\sa-z]*/is", $version, $maches)) {
			return $maches[1]*1000000 + $maches[2]*1000 + $maches[3];
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
 * baseUrlを除外したURLのパラメーターを取得する
 * 先頭のスラッシュは除外する
 */
	function getParamsFromEnv() {
		$appBaseUrl = Configure::read('App.baseUrl');
		$parameter = '';
		if($appBaseUrl) {
			$base = dirname($appBaseUrl);
			if(strpos($_SERVER['REQUEST_URI'], $appBaseUrl) !== false) {
				$parameter = str_replace($appBaseUrl,'',$_SERVER['REQUEST_URI']);
			}else {
				// トップページ
				$parameter = str_replace($base.'/','',$_SERVER['REQUEST_URI']);
			}
		}else {
			$parameter = '';
			$query = $_SERVER['QUERY_STRING'];
			if(!empty($query)){
				if(strpos($query, '&')){
					$queries = split('&',$query);
					foreach($queries as $_query) {
						if(strpos($_query, '=')){
							list($key,$value) = split('=',$_query);
							if($key=='url'){
								$parameter = $value;
								break;
							}
						}
					}
				}else{
					if(strpos($query, '=')){
						list($key,$value) = split('=',$query);
						if($key=='url'){
							$parameter = $value;
						}
					}
				}

			}elseif ($_SERVER['REQUEST_URI'] == baseUrl().'index'){
					// さくらインターネットで、/index とした場合、QUERY_STRINGが空になってしまう
					$parameter = 'index';
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
				}
				if(Configure::read('App.baseUrl')) {
					if($home){
						$home = 'index_php_'.$home;
					}else{
						$home = 'index_php';
					}
				}elseif(!$home){
					$home = 'home';
				}
				clearCache($home);
			}
		}elseif($url) {
			clearCache(strtolower(Inflector::slug($url)),'views',$ext);
		}else {
			App::import('Core','Folder');
			$folder = new Folder(CACHE.'views'.DS);
			$files = $folder->read(true,true);
			foreach($files[1] as $file) {
				if($file != 'empty') {
					if($ext) {
						if(preg_match('/'.str_replace('.', '\.', $ext).'$/is', $file)) {
							@unlink(CACHE.'views'.DS.$file);
						}
					}else {
						@unlink(CACHE.'views'.DS.$file);
					}
				}
			}
		}

	}
/**
 * キャッシュファイルを全て削除する
 */
	function clearAllCache() {

		/* 標準の関数だとemptyファイルまで削除されてしまい、開発時に不便なのでFolderクラスで削除
			Cache::clear();
			Cache::clear(false,'_cake_core_');
			Cache::clear(false,'_cake_model_');
			clearCache();
		*/

		App::import('Core','Folder');
		$folder = new Folder(CACHE);

		$files = $folder->read(true,true,true);
		foreach($files[1] as $file) {
			@unlink($file);
		}
		foreach($files[0] as $dir) {
			$folder = new Folder($dir);
			$caches = $folder->read(true,true,true);
			foreach($caches[1] as $file) {
				if(basename($file) != 'empty') {
					@unlink($file);
				}
			}
		}

	}
/**
 * BaserCMSのインストールが完了しているかチェックする
 * @return	boolean
 */
	function isInstalled () {
		if(file_exists(CONFIGS.'database.php') && file_exists(CONFIGS.'install.php')){
			require_once CONFIGS.'database.php';
			$dbConfig = new DATABASE_CONFIG();
			if(!empty($dbConfig->baser['driver'])){
				return true;
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
		$folder->create(CACHE);
		$folder->create(CACHE.'models',0777);
		$folder->create(CACHE.'persistent',0777);
		$folder->create(CACHE.'views',0777);
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
?>