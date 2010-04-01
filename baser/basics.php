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
    function baseUrl(){
        $appBaseUrl = Configure::read('App.baseUrl');
        if($appBaseUrl){
            $_url = $_SERVER['REQUEST_URI'];
            $baseUrl = $appBaseUrl.'/';
        }else{
            $_baseUrl = str_replace(DS,'/',preg_replace("/^\\".DS."/i",'',str_replace(docRoot(),'',ROOT)));
            if($_baseUrl){
                $baseUrl = '/'.$_baseUrl.'/';
            }else{
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
    function docRoot(){
    	$docRoot = str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['SCRIPT_FILENAME']);
        return str_replace('/', DS, $docRoot);
    }
/**
 * リビジョンを取得する
 * @param string    BaserCMS形式のバージョン表記　（例）BaserCMS 1.5.3.1600 beta
 * @return string   リビジョン番号
 */
    function revision($version){
        return preg_replace("/BaserCMS [0-9]+?\.[0-9]+?\.[0-9]+?\.([0-9]*)[\sa-z]*/is", "$1", $version);
    }
/**
 * バージョンを特定する一意の数値を取得する
 * @param string $version
 */
	function verpoint($version){
		if(preg_match("/BaserCMS ([0-9]+)\.([0-9]+)\.([0-9]+)[\sa-z]*/is", $version, $maches)){
			return $maches[1]*100 + $maches[2]*10 + $maches[3];
		}else{
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
			if(!empty($info['extension'])){
				return $info['extension'];
			}else{
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
	function getParamsFromEnv(){
		$appBaseUrl = Configure::read('App.baseUrl');
		$parameter = '';
		if($appBaseUrl){
			$base = dirname($appBaseUrl);
			if(strpos($_SERVER['REQUEST_URI'], $appBaseUrl) !== false){
				$parameter = str_replace($appBaseUrl,'',$_SERVER['REQUEST_URI']);
			}else{
				// トップページ
				$parameter = str_replace($base.'/','',$_SERVER['REQUEST_URI']);
			}
		}else{
			$query = $_SERVER['QUERY_STRING'];
			if(!empty($query) && strpos($query, '=')){
				$aryPath = explode('=',$query);
				if($aryPath[0]=='url'){
					$parameter = $aryPath[1];
				}
			}else{
				$parameter = '';
			}
		}
		$parameter = preg_replace('/^\//','',$parameter);
		return $parameter;
	}
?>