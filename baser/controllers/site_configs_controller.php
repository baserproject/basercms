<?php
/* SVN FILE: $Id$ */
/**
 * サイト設定コントローラー
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * サイト設定コントローラー
 *
 * @package			baser.controllers
 */
class SiteConfigsController extends AppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'SiteConfigs';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('GlobalMenu');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
   var $components = array('Auth','Cookie','AuthConfigure');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ヘルパー
 * @var array 
 */
    var $helpers = array('FormEx');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form');
/**
 * [ADMIN] サイト基本設定
 *
 * @return	void
 * @access 	public
 */
	function admin_form(){

		if(empty($this->data)){
			$this->data = $this->SiteConfig->read(null, 1);
            $this->data['SiteConfig'] = $this->siteConfigs;
            $this->data['SiteConfig']['mode'] = $this->readDebug();
		}else{
            // テーブル構造が特殊なので強引にバリデーションを行う
            $this->SiteConfig->data = $this->data;
            if(!$this->SiteConfig->validates()){
                $this->Session->setFlash('入力エラーです。内容を修正してください。');
            }else{
                // KeyValueへ変換処理
                $mode = $this->data['SiteConfig']['mode'];
                unset($this->data['SiteConfig']['mode']);
                unset($this->data['SiteConfig']['id']);
                $this->SiteConfig->saveKeyValue($this->data);

                // ビューのキャッシュを削除
                $this->deleteViewCache();
                $this->writeDebug($mode);
                $this->Session->setFlash('システム設定を保存しました。');
                $this->redirect(array('action'=>'form'));
            }
		}

        // バックアップ機能を実装しているデータベースの場合のみバックアップへのリンクを表示
        $enableBackupDb = array('sqlite','sqlite3','mysql','csv');
        $dbConfigs = new DATABASE_CONFIG();
        $dbConfig = $dbConfigs->{'baser'};
        $driver = str_replace('_ex','',$dbConfig['driver']);
        if(in_array($driver,$enableBackupDb)){
            $this->set('backupEnabled',true);
        }

        // テーマの一覧を取得
        $this->set('themes',$this->SiteConfig->getThemes());

		// 表示設定
        $this->subMenuElements = array('site_configs');
		$this->pageTitle = 'サイト基本設定';

	}
/**
 * [ADMIN] バックアップデータを作成する
 *
 * mysql/csvdb のバックアップを [app/tmp/backup/database]に保存し、
 * zip形式にアーカイブした状態でダウンロードできる
 *
 * @param	boolean	バックアップアーカイブをクリアする場合は、true を設定
 * @return 	void
 * @access	public
 */
	function admin_backup_data($blnClearOldArchives = false){

		$backupDir = TMP.'backup'.DS;
		if(!file_exists($backupDir)){
			mkdir($backupDir);
			chmod($backupDir,0777);
		}
		$backupPath = $backupDir . 'database'.DS;
		if(!file_exists($backupPath)){
			mkdir($backupPath);
			chmod($backupPath,0777);
		}
		$dbConfigs = new DATABASE_CONFIG();
		foreach($dbConfigs as $key => $dbConfig){
			if($key != 'test'){
				$this->{str_replace('Ex','','_backup'.Inflector::camelize($dbConfig['driver']))}($dbConfig,$backupPath);
			}
		}
		// ZIP圧縮して出力
		App::import('Vendor','createzip');
		$createZip = new createDirZip;
		$createZip->get_files_from_folder($backupPath,'/');

		$archiveDir = $backupDir.'archives'.DS;
		if(!file_exists($archiveDir)){
			mkdir($archiveDir);
			chmod($archiveDir,0777);
		}

		$fileName = $archiveDir.date('Ymd_His').'_backup.zip';
		$fd = fopen ($fileName, "wb");
		$out = fwrite ($fd, $createZip->getZippedfile());
		fclose ($fd);

		if($blnClearOldArchives){
			$dir = dir($archiveDir);
			while(($file = $dir->read()) !== false){
				if($file != '.' && $file != '..'){
					$_file = $archiveDir.$file;
					if($_file != $fileName){
						@unlink($_file);
					}
				}
			}
		}

		$createZip->forceDownload($fileName);
		exit();

	}
/**
 * CSVのデータをバックアップ
 * TODO DBOに移行する
 *
 * @param	array	データベース設定情報
 * @param	string	保存先パス
 * @return 	boolean
 * @access	protected
 */
	function _backupCsv($config,$savePath){

		$csvDir = 'csv';
		$csvPath = APP.'db'.DS.$csvDir;
		$_savePath = $savePath.$csvDir;

		if(!file_exists($savePath)){
			mkdir($savePath);
			chmod($savePath,0777);
		}
		if(!file_exists($_savePath)){
			mkdir($_savePath);
			chmod($_savePath,0777);
		}

		return $this->_backupDir($csvPath,$_savePath);

	}
/**
 * SQLite3のバックアップ
 * TODO DBOに移行する
 * @param array $config
 * @param string $savePath
 * @return boolean
 */
    function _backupSqlite3($config,$savePath){

		$dir = 'sqlite';
		$path = $config['database'];
        $dbName = basename($config['database']);
		$_savePath = $savePath.$dir;

		if(!file_exists($savePath)){
			mkdir($savePath);
			chmod($savePath,0777);
		}
		if(!file_exists($_savePath)){
			mkdir($_savePath);
			chmod($_savePath,0777);
		}

        return copy($path,$_savePath.DS.$dbName);

    }
/**
 * ディレクトリごとファイルバックアップする
 *
 * ※ ただし、defaultという名称のディレクトリはコピーしない
 * TODO 除外オプションをつけるべき
 *
 * @param	string	コピー元ディレクトリパス
 * @param	string	コピー先ディレクトリパス
 * @return 	boolean
 * @access	protected
 */
	function _backupDir($targetPath,$savePath){

		$dir = dir($targetPath);
		while(($file = $dir->read()) !== false){
			if($file != '.' && $file != '..' && $file != 'default'){
				$_targetPath = $targetPath.DS.$file;
				$_savePath = $savePath.DS.$file;
				if(is_dir($_targetPath)){
					if(!file_exists($_savePath)){
						mkdir($_savePath);
						chmod($_savePath,0777);
					}
					$this->_backupDir($_targetPath,$_savePath);
				}else{
					copy($_targetPath,$_savePath);
					chmod($_savePath,0666);
				}
			}
		}
		return true;

	}
/**
 * MySqlのデータをバックアップ
 * TODO DBOに移行する
 * @param	array	データベース設定情報
 * @param	string	保存先パス
 * @return 	boolean
 * @access	protected
 */
	function _backupMysql($config,$savePath){

		$_savePath = $savePath.'mysql'.DS;
		if(!file_exists($_savePath)){
			mkdir($_savePath);
			chmod($_savePath,0777);
		}

		App::import('Vendor','mysqldump');
		$connection = @mysql_connect($config['host'],$config['login'],$config['password']);
		$sql = "SET NAMES utf8";
		mysql_query($sql);
		$filename = $_savePath.$config['database'].'.sql';

		$dump = new MySQLDump($config['database'],$filename);
                $dump->doDump();

	}
/**
 * MySqlのデータをバックアップ(mysql_logドライバ用）
 *
 * @param	array	データベース設定情報
 * @param	string	保存先パス
 * @return 	boolean
 * @access	protected
 */
	function _backupMysqlLog($config,$savePath){

        $this->_backupMysql($config,$savePath);

	}
}
?>