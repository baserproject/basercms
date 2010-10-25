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
		if(!is_dir($path)){
			$Folder = new Folder($path, true, 0777);
			if(!is_writable($path)){
				$this->Session->setFlash('フォルダ：'.$path.' が存在するか確認し、書込権限を与えてください。');
			}
		}
		
		if($this->data) {
			if(!$this->data['Tool']) {
				$this->Session->setFlash('モデル名を入力してください。');
			}else {
				if($this->Tool->writeSchema($this->data, $path)) {
					$this->data = null;
					$this->Session->setFlash('スキーマファイルを生成しました。');
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
	function admin_load() {
		if($this->data) {
			if(!$this->data['Schema']['model']) {
				$this->Session->setFlash('モデル名を入力してください。');
			}else {
				$db =& ConnectionManager::getDataSource('baser');
				if($db->createTableSchema(array('model'=>$this->data['Schema']['model'],'path'=>BASER_CONFIGS.'sql'))) {
					$this->data = null;
					$this->Session->setFlash('スキーマファイルを読み込みしました。');
				}else {
					$this->Session->setFlash('スキーマファイルの読み込みに失敗しました。');
				}
			}
		}
		/* 表示設定 */
		$this->pageTitle = 'スキーマファイル読込';
	}
}
?>