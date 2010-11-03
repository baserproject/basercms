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
	var $uses = array('SiteConfig','GlobalMenu','Page');
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
			$this->data['SiteConfig']['smart_url'] = $this->readSmartUrl();
		}else {
			// テーブル構造が特殊なので強引にバリデーションを行う
			$this->SiteConfig->data = $this->data;
			if(!$this->SiteConfig->validates()) {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}else {
				// KeyValueへ変換処理
				$mode = $this->data['SiteConfig']['mode'];
				$smartUrl = $this->data['SiteConfig']['smart_url'];
				unset($this->data['SiteConfig']['mode']);
				unset($this->data['SiteConfig']['id']);
				unset($this->data['SiteConfig']['smart_url']);
				$this->SiteConfig->saveKeyValue($this->data);
				$this->writeDebug($mode);
				if($this->readSmartUrl() != $smartUrl) {
					$this->writeSmartUrl($smartUrl);
				}
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

				if($this->readSmartUrl() != $smartUrl) {
					if($smartUrl){
						header('Location: '.$this->getRewriteBase('/admin/site_configs/form'));
					}else{
						header('Location: '.$this->getRewriteBase('/index.php/admin/site_configs/form'));
					}
				}else{
					$this->redirect(array('action'=>'form'));
				}
				
			}
		}

		/* スマートURL関連 */
		// mod_rewrite モジュールインストール
		$apachegetmodules = function_exists('apache_get_modules');
		if($apachegetmodules) {
			$rewriteInstalled = in_array('mod_rewrite',apache_get_modules());
		}else {
			$rewriteInstalled = -1;
		}
		$writableInstall = is_writable(CONFIGS.'install.php');
		$writableHtaccess = is_writable(ROOT.DS.'.htaccess');
		$writableHtaccess2 = is_writable(WWW_ROOT.'.htaccess');
		if($writableInstall && $writableHtaccess && $writableHtaccess2 && $rewriteInstalled !== false){
			$smartUrlChangeable = true;
		} else {
			$smartUrlChangeable = false;
		}
		// バックアップ機能を実装しているデータベースの場合のみバックアップへのリンクを表示
		$enableBackupDb = array('sqlite','sqlite3','mysql','csv','postgres');
		$dbConfigs = new DATABASE_CONFIG();
		$dbConfig = $dbConfigs->{'baser'};
		$driver = str_replace('_ex','',$dbConfig['driver']);
		if(in_array($driver,$enableBackupDb)) {
			$this->set('backupEnabled',true);
		}

		$this->set('themes',$this->SiteConfig->getThemes());
		$this->set('rewriteInstalled', $rewriteInstalled);
		$this->set('writableInstall', $writableInstall);
		$this->set('writableHtaccess', $writableHtaccess);
		$this->set('writableHtaccess2', $writableHtaccess2);
		$this->set('smartUrlChangeable', $smartUrlChangeable);
		$this->subMenuElements = array('site_configs');
		$this->pageTitle = 'サイト基本設定';

	}
/**
 * スマートURLの設定を取得
 * 
 * @return	boolean
 * @access	public
 */
	function readSmartUrl(){
		if (Configure::read('App.baseUrl')) {
			return false;
		} else {
			return true;
		}
	}
/**
 * スマートURLの設定を行う
 * 
 * @param	boolean	$smartUrl
 * @return	boolean
 * @access	public
 */
	function writeSmartUrl($smartUrl) {

		/* install.php の編集 */
		$baseUrlPattern = '/Configure\:\:write[\s]*\([\s]*\'App\.baseUrl\'[\s]*,[\s]*\'([^\']*)\'[\s]*\);\n/is';
		$file = new File(CONFIGS.'install.php');
		$data = $file->read();
		if($smartUrl) {
			if(preg_match($baseUrlPattern, $data, $matches)) {
				$data = preg_replace($baseUrlPattern, "Configure::write('App.baseUrl', '');\n", $data);
			} else {
				$data = str_replace("<?php\n", "<?php\nConfigure::write('App.baseUrl', '');\n", $data);
			}
		} else {
			$data = preg_replace($baseUrlPattern, '', $data);
		}
		$file->write($data);
		$file->close();
		
		/* /.htaccess の編集 */
		$rewritePatterns = array(	"(/\n|)[^\n]*RewriteEngine\s+on/is",
									"/\n[^\n]*RewriteBase.+$/is",
									"/\n[^\n]*RewriteRule\s+\^\$\s+app\/webroot\/\s+\[L\]/is",
									"/\n[^\n]*RewriteRule\s+\(\.\*\)\s+app\/webroot\/\$1\s+\[L\]/is");
		$rewriteSettings = array(	'RewriteEngine on',
									'RewriteBase '.$this->getRewriteBase('/'),
									'RewriteRule ^$ app/webroot/ [L]',
									'RewriteRule (.*) app/webroot/$1 [L]');
		$path = ROOT.DS.'.htaccess';
		$file = new File($path);
		$data = $file->read();
		foreach ($rewritePatterns as $rewritePattern) {
			$data = preg_replace($rewritePattern, '', $data);
		}
		if($smartUrl) {
			$data .= implode("\n", $rewriteSettings);
		}
		$file->write($data);
		$file->close();

		/* /app/webroot/.htaccess の編集 */
		$rewritePatterns = array(	"(/\n|)[^\n]*RewriteEngine\s+on/is",
									"/\n[^\n]*RewriteBase.+$/is",
									"/\n[^\n]*RewriteCond\s+\%\{REQUEST_FILENAME\}\s+\!\-d/is",
									"/\n[^\n]*RewriteCond\s+\%\{REQUEST_FILENAME\}\s+\!\-s/is",
									"/\n[^\n]*RewriteRule\s+\^\(\.\*\)\$\s+index\.php\?url\=\$1\s\[QSA,L\]/is");
		$rewriteSettings = array(	'RewriteEngine on',
									'RewriteBase '.$this->getRewriteBase('/app/webroot'),
									'RewriteCond %{REQUEST_FILENAME} !-d',
									'RewriteCond %{REQUEST_FILENAME} !-f',
									'RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]');
		$path = WWW_ROOT.'.htaccess';
		$file = new File($path);
		$data = $file->read();
		foreach ($rewritePatterns as $rewritePattern) {
			$data = preg_replace($rewritePattern, '', $data);
		}
		if($smartUrl) {
			$data .= implode("\n", $rewriteSettings);
		}
		$file->write($data);
		$file->close();
		
	}
/**
 * RewriteBase の設定を取得する
 *
 * @param	string	$base
 * @return	string
 */
	function getRewriteBase($url){

		$baseUrl = baseUrl();
		if(preg_match("/index\.php/", $baseUrl)){
			$baseUrl = str_replace('index.php/', '', baseUrl());
		}
		$baseUrl = preg_replace("/\/$/",'',$baseUrl);
		if($url != '/' || !$baseUrl) {
			$url = $baseUrl.$url;
		}else{
			$url = $baseUrl;
		}
		
		return $url;

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
		$fileName = date('Ymd_His') . '_backup';		
		App::import('Vendor','Createzip');
		$Createzip = new Createzip;
		$Createzip->addFolder($backupPath, '');
		$Createzip->download($fileName);
		exit();

	}
/**
 * [ADMIN] PHPINFOを表示する
 * @return void
 * @access public
 */
	function admin_info() {

		$this->pageTitle = '環境情報';
		$drivers = array('csv'=>'CSV','sqlite3_ex'=>'SQLite3','mysql_ex'=>'MySQL','postgres'=>'PostgreSQL');
		$smartUrl = 'ON';
		$db =& ConnectionManager::getDataSource('baser');
		if(Configure::read('App.baseUrl')){
			$smartUrl = 'OFF';
		}
		$this->set('driver',$drivers[$db->config['driver']]);
		$this->set('smartUrl',$smartUrl);
		$this->set('baserVersion',$this->siteConfigs['version']);
		$this->set('cakeVersion',$this->getCakeVersion());
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