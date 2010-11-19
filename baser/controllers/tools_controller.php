<?php
/* SVN FILE: $Id$ */
/**
 * ツールコントローラー
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
 * ツールコントローラー
 *
 * @package			baser.controllers
 */
class ToolsController extends AppController {
/**
 * クラス名
 *
 * @var     string
 * @access  public
 */
	var $name = 'Tools';
	var $uses = array('Tool', 'Page');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ヘルパ
 * 
 * @var array
 */
	var $helpers = array('FormEx');
/**
 * サブメニュー
 */
	var $subMenuElements = array('tools');
/**
 * データメンテナンス
 *
 * @param	string	$mode
 */
	function admin_maintenance($mode='') {

		switch($mode) {
			case 'backup':
				$this->_backupDb();
				break;
			case 'restore':
				if(!$this->data) {
					$this->notFound();
				}
				$messages = array();
				if($this->_restoreDb($this->data)) {
					$messages[] = 'データの復元が完了しました。';
				} else {
					$messages[] = 'データの復元に失敗しました。';
				}
				if(!$this->Page->createAllPageTemplate()){
					$messages[] = 'ページテンプレートの生成に失敗しました。<br />表示できないページはページ管理より更新処理を行ってください。';
				}
				if($messages) {
					$this->Session->setFlash(implode('<br />', $messages));
				}
				$this->redirect(array('action'=>'maintenance'));
				break;
		}
		$this->pageTitle = 'データメンテナンス';
		$this->subMenuElements = array('site_configs');

	}
/**
 * バックアップファイルを復元する
 *
 * @param	array	$data
 * @return	boolean
 * @access	protected
 */
	function _restoreDb($data){
		
		if(empty($data['Tool']['backup']['tmp_name'])){
			return false;
		}
		
		$tmpPath = TMP.'schemas'.DS;
		$targetPath = $tmpPath.$data['Tool']['backup']['name'];

		if(!move_uploaded_file($data['Tool']['backup']['tmp_name'], $targetPath)) {
			return false;
		}

		/* ZIPファイルを解凍する */
		App::import('Vendor', 'Simplezip');
		$Simplezip = new Simplezip();
		if(!$Simplezip->unzip($targetPath, $tmpPath)){
			return false;
		}
		@unlink($targetPath);

		if(!$this->_loadBackup($tmpPath.'baser'.DS, 'baser')) {
			return false;
		}
		if(!$this->_loadBackup($tmpPath.'plugin'.DS, 'plugin')) {
			return false;
		}

		$this->_resetTmpSchemaFolder();
		clearAllCache();
		
		return true;
		
	}
/**
 * データベースをレストア
 *
 * @param	string	$path			スキーマファイルのパス
 * @param	string	$configKeyName	DB接続名
 * @return	boolean
 * @access	protected
 */
	function _loadBackup($path, $configKeyName) {

		$Folder = new Folder($path);
		$files = $Folder->read(true, true);
		if(!is_array($files[1])){
			return false;
		}
		
		$db =& ConnectionManager::getDataSource($configKeyName);
		
		/* テーブルを削除する */
		foreach($files[1] as $file) {
			if(preg_match("/\.php$/", $file)) {
				if(!$db->loadSchema(array('type'=>'drop','path' => $path, 'file'=> $file))){
					return false;
				}
			}
		}

		/* テーブルを読み込む */
		foreach($files[1] as $file) {
			if(preg_match("/\.php$/", $file)) {
				if(!$db->loadSchema(array('type'=>'create','path' => $path, 'file'=> $file))){
					return false;
				}
			}
		}

		/* CSVファイルを読み込む */
		foreach($files[1] as $file) {
			if(preg_match("/\.csv$/", $file)) {
				if(!$db->loadCsv(array('path' => $path.$file, 'encoding' => 'SJIS'))){
					return false;
				}
			}
		}

		return true;
		
	}
/**
 * バックアップデータを作成する
 *
 * @return 	void
 * @access	public
 */
	function _backupDb() {

		$tmpDir = TMP . 'schemas' . DS;
		$version = $this->getBaserVersion();
		$Folder = new Folder();
		$Folder->delete($tmpDir);
		$Folder->create($tmpDir.'baser'.DS, 0777);
		$Folder->create($tmpDir.'plugin'.DS, 0777);
		$this->_writeBackup('baser', $tmpDir.'baser'.DS);
		$this->_writeBackup('plugin', $tmpDir.'plugin'.DS);

		// ZIP圧縮して出力
		$fileName = 'baserbackup_'.$version.'_'.date('Ymd_His');
		App::import('Vendor','Simplezip');
		$Simplezip = new Simplezip;
		$Simplezip->addFolder($tmpDir);
		$Simplezip->download($fileName);
		$this->_resetTmpSchemaFolder();
		exit();

	}
/**
 * バックアップファイルを書きだす
 *
 * @param	string	$configKeyName
 * @param	string	$path
 * @return	boolean;
 */
	function _writeBackup($configKeyName, $path) {

		$db =& ConnectionManager::getDataSource($configKeyName);
		$db->cacheSources = false;
		$tables = $db->listSources();
		foreach($tables as $table) {
			if(preg_match("/^".$db->config['prefix']."([^_].+)$/", $table, $matches) &&
					!preg_match("/^".Configure::read('Baser.pluginDbPrefix')."[^_].+$/", $matches[1])) {
				$table = $matches[1];
				$model = Inflector::classify(Inflector::singularize($table));
				if(!$db->writeSchema(array('path'=>$path, 'model'=>$model))){
					return false;
				}
				if(!$db->writeCsv(array('path'=>$path.$table.'.csv'))) {
					return false;
				}
			}
		}
		return true;

	}
/**
 * モデル名からスキーマファイルを生成する
 *
 * @return  void
 * @access  public
 */
	function admin_write_schema() {

		$path = TMP.'schemas'.DS;
		
		if(!$this->data) {
			$this->data['Tool']['connection'] = 'baser';
		} else {
			if(empty($this->data['Tool'])) {
				$this->Session->setFlash('テーブルを選択してください。');
			}else {
				if(!$this->_resetTmpSchemaFolder()){
					$this->Session->setFlash('フォルダ：'.$path.' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。');
					$this->redirect(array('action'=>'admin_write_schema'));
				}
				if($this->Tool->writeSchema($this->data, $path)) {
					App::import('Vendor','Simplezip');
					$Simplezip = new Simplezip();
					$Simplezip->addFolder($path);
					$Simplezip->download('schemas');
					exit();
				}else {
					$this->Session->setFlash('スキーマファイルの生成に失敗しました。');
				}
			}
		}

		/* 表示設定 */
		$this->pageTitle = 'スキーマファイル生成';

	}
/**
 * スキーマファイルを読み込みテーブルを生成する
 *
 * @return  void
 * @access  public
 */
	function admin_load_schema() {
		
		if(!$this->data) {
			$this->data['Tool']['schema_type'] = 'create';
		} else {
			if(is_uploaded_file($this->data['Tool']['schema_file']['tmp_name'])) {
				$path = TMP.'schemas'.DS;
				if(!$this->_resetTmpSchemaFolder()){
					$this->Session->setFlash('フォルダ：'.$path.' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。');
					$this->redirect(array('action'=>'admin_load_schema'));
				}
				if($this->Tool->loadSchema($this->data, $path)) {
					$this->Session->setFlash('スキーマファイルの読み込みに成功しました。');
					$this->redirect(array('action'=>'admin_load_schema'));
				} else {
					$this->Session->setFlash('スキーマファイルの読み込みに失敗しました。');
				}
			}else {
				$this->Session->setFlash('ファイルアップロードに失敗しました。');
			}
		}
		/* 表示設定 */
		$this->pageTitle = 'スキーマファイル読込';
		
	}
/**
 * スキーマ用の一時フォルダをリセットする
 *
 * @return boolean
 */
	function _resetTmpSchemaFolder() {
		
		$path = TMP.'schemas'.DS;
		if(is_dir($path) && !is_writable($path)){
			return false;
		}
		$Folder = new Folder();
		$Folder->delete($path);
		$Folder->create($path, 0777);
		return true;
		
	}
	
}
?>