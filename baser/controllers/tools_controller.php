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
	var $uses = array('Tool');
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
 * モデル名からスキーマファイルを生成する
 *
 * @return  void
 * @access  public
 */
	function admin_write_schema() {

		$path = TMP.'schemas'.DS;
		
		if($this->data) {
			if(empty($this->data['Tool']['tables'])) {
				$this->Session->setFlash('テーブルを選択してください。');
			}else {
				if(!$this->_resetTmpSchemaFolder()){
					$this->Session->setFlash('フォルダ：'.$path.' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。');
					$this->redirect(array('action'=>'admin_write_schema'));
				}
				if($this->Tool->writeSchema($this->data, $path)) {
					App::import('Vendor','Createzip');
					$Createzip = new Createzip();
					$Createzip->addFolder($path);
					$Createzip->download('schemas');
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