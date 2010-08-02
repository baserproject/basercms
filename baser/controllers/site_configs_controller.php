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
	var $uses = array('SiteConfig','GlobalMenu','Page','WidgetArea');
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
 * @var		array
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form');
/**
 * Folder Object
 *
 * @var Folder
 */
	var $Folder;
/**
 * beforeFilter
 * @return	void
 * @access	public
 */
	function beforeFilter() {

		parent::beforeFilter();

		// init Folder
		$this->Folder =& new Folder();
		$this->Folder->mode = 0777;

	}
/**
 * [ADMIN] サイト基本設定
 *
 * @return	void
 * @access 	public
 */
	function admin_form() {

		if(empty($this->data)) {
			$this->data = $this->SiteConfig->read(null, 1);
			$this->data['SiteConfig'] = $this->siteConfigs;
			$this->data['SiteConfig']['mode'] = $this->readDebug();
		}else {
			// テーブル構造が特殊なので強引にバリデーションを行う
			$this->SiteConfig->data = $this->data;
			if(!$this->SiteConfig->validates()) {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}else {
				// KeyValueへ変換処理
				$mode = $this->data['SiteConfig']['mode'];
				unset($this->data['SiteConfig']['mode']);
				unset($this->data['SiteConfig']['id']);
				$this->SiteConfig->saveKeyValue($this->data);
				$this->writeDebug($mode);
				if($this->siteConfigs['maintenance'] || ($this->siteConfigs['theme'] != $this->data['SiteConfig']['theme'])){
					clearViewCache();
				}
				if($this->siteConfigs['theme'] != $this->data['SiteConfig']['theme']) {
					if(!$this->Page->createAllPageTemplate()){
						$this->Session->setFlash('テーマ変更中にページテンプレートの生成に失敗しました。<br />表示できないページはページ管理より更新処理を行ってください。');
						$this->redirect(array('action'=>'form'));
					}
				}
				$this->Session->setFlash('システム設定を保存しました。');
				$this->redirect(array('action'=>'form'));
			}
		}

		// バックアップ機能を実装しているデータベースの場合のみバックアップへのリンクを表示
		$enableBackupDb = array('sqlite','sqlite3','mysql','csv','postgres');
		$dbConfigs = new DATABASE_CONFIG();
		$dbConfig = $dbConfigs->{'baser'};
		$driver = str_replace('_ex','',$dbConfig['driver']);
		if(in_array($driver,$enableBackupDb)) {
			$this->set('backupEnabled',true);
		}

		// テーマの一覧を取得
		$this->set('themes',$this->SiteConfig->getThemes());

		// 表示設定
		$this->subMenuElements = array('site_configs');
		$this->pageTitle = 'サイト基本設定';

	}
/**
 * キャッシュファイルを全て削除する
 * @return	void
 * @access	public
 */
	function admin_del_cache() {
		clearAllCache();
		$this->Session->setFlash('サーバーキャッシュを削除しました。');
		$this->redirect(array('action'=>'form'));
	}
/**
 * [ADMIN] バックアップデータを作成する
 *
 * データベースのバックアップを [app/tmp/backup/database]に保存し、
 * zip形式にアーカイブした状態でダウンロードできる
 *
 * TODO 現在、PostgreSQLの場合、DB接続設定ごとにファイルが生成されるが、
 * 他のDBについては、１回で全てのDBのデータを出力する仕様となってしまっている。
 * 同じDBの場合はただの上書き。違うDBを利用した場合には、正常にバックアップがとれない。
 *
 * @param	boolean	バックアップアーカイブをクリアする場合は、true を設定
 * @return 	void
 * @access	public
 */
	function admin_backup_data($blnClearOldArchives = false) {

		$backupDir = TMP . 'backup' . DS;
		$backupPath = $backupDir . 'database' . DS;
		$this->Folder->delete($backupPath);
		$this->Folder->create($backupDir,0777);
		$this->Folder->create($backupPath,0777);

		$dbConfigs = new DATABASE_CONFIG();
		foreach ($dbConfigs as $key => $dbConfig) {

			$backupMethodName = preg_replace('/Ex$/', '', '_backup' . Inflector::camelize($dbConfig['driver']));

			if($key != 'test' && method_exists($this, $backupMethodName)) {
				call_user_func_array(array($this, $backupMethodName), array($dbConfig, $backupPath, $key));
			}

		}
		// ZIP圧縮して出力
		App::import('Vendor','createzip');
		$createZip = new createDirZip;
		$createZip->get_files_from_folder($backupPath, '/');

		$archiveDir = $backupDir . 'archives' . DS;
		if($blnClearOldArchives) {
			$this->Folder->delete($archiveDir);
		}
		$this->Folder->create($archiveDir);

		$fileName = $archiveDir . date('Ymd_His') . '_backup.zip';
		$fd = fopen ($fileName, "wb");
		$out = fwrite ($fd, $createZip->getZippedfile());
		fclose ($fd);

		$createZip->forceDownload($fileName);
		exit();

	}
/**
 * [ADMIN] PHPINFOを表示する
 * @return void
 * @access public
 */
	function admin_phpinfo() {
		$this->pageTitle = 'PHP設定情報';
		$this->subMenuElements = array('site_configs');
	}

/**
 * MySqlのデータをバックアップ
 * TODO SQLDumperに移行する
 * @param	array	データベース設定情報
 * @param	string	保存先パス
 * @return 	boolean
 * @access	protected
 */
	function _backupMysql($config,$savePath) {

		$_savePath = $savePath.'mysql'.DS;
		$this->Folder->create($_savePath,0777);

		App::import('Vendor','mysqldump');
		$connection = @mysql_connect($config['host'],$config['login'],$config['password']);
		$sql = "SET NAMES utf8";
		mysql_query($sql);
		$filename = $_savePath.$config['database'].'.sql';

		$dump = new MySQLDump($config['database'],$filename);
		$dump->doDump();

	}
/**
 * SQLite3のバックアップ
 * TODO SQLDumperに移行する
 * @param array $config
 * @param string $savePath
 * @return boolean
 */
	function _backupSqlite3($config,$savePath) {

		$dir = 'sqlite';
		$path = $config['database'];
		$dbName = basename($config['database']);
		$_savePath = $savePath.$dir;

		$this->Folder->create($savePath,0777);
		$this->Folder->create($_savePath,0777);

		return copy($path,$_savePath.DS.$dbName);

	}
/**
 * PostgreSQLのデータをバックアップ
 * @param	array	データベース設定情報
 * @param	string	保存先パス
 * @key		string	DB設定名
 * @return 	boolean
 * @access	protected
 */
	function _backupPostgres($config, $savePath, $configName) {

		$_savePath = $savePath . 'postgres' . DS;
		$this->Folder->create($_savePath,0777);

		App::import('Vendor', 'SqlDumper', array('file' => 'sql_dumper' . DS . 'sql_dumper.php'));

		/* @var SqlDumper $sqlDumpr */
		$sqlDumper =& ClassRegistry::init('SqlDumper', 'Vendor');
		$sqlDumper->file_prefix = '';
		$sqlDumper->file_suffix = '_' . $configName . '.sql';
		$sqlDumper->process($config['database'], null, $_savePath);

	}
/**
 * CSVのデータをバックアップ
 * TODO SQLDumperに移行する（ただし、ドライバーも対応させる必要がある）
 * @param	array	データベース設定情報
 * @param	string	保存先パス
 * @return 	boolean
 * @access	protected
 */
	function _backupCsv($config,$savePath) {

		$csvDir = 'csv';
		$csvPath = APP.'db'.DS.$csvDir;
		$_savePath = $savePath.$csvDir;

		$this->Folder->create($savePath,0777);
		$this->Folder->create($_savePath,0777);

		return $this->_backupDir($csvPath,$_savePath);

	}
/**
 * ディレクトリごとファイルバックアップする
 * ※ ただし、defaultという名称のディレクトリはコピーしない
 * TODO 除外オプションをつけるべき
 * @param	string	コピー元ディレクトリパス
 * @param	string	コピー先ディレクトリパス
 * @return 	boolean
 * @access	protected
 */
	function _backupDir($targetPath,$savePath) {

		$dir = dir($targetPath);
		while(($file = $dir->read()) !== false) {
			if($file != '.' && $file != '..' && $file != 'default') {
				$_targetPath = $targetPath.DS.$file;
				$_savePath = $savePath.DS.$file;
				if(is_dir($_targetPath)) {
					$this->Folder->create($_savePath,0777);
					$this->_backupDir($_targetPath,$_savePath);
				}else {
					copy($_targetPath,$_savePath);
					chmod($_savePath,0666);
				}
			}
		}
		return true;

	}

}
?>