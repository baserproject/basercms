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
            $_baseUrl = str_replace(DS,'/',preg_replace('/^\//i','',str_replace(docRoot(),'',ROOT)));
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
        return str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['SCRIPT_FILENAME']);
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
?>