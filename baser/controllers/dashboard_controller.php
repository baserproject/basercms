<?php
/* SVN FILE: $Id$ */
/**
 * ダッシュボードコントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ダッシュボードコントローラー
 * 管理者ログインやメンバーログインのダッシュボードページを表示する
 *
 * @package baser.controllers
 */
class DashboardController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Dashboard';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Dblog','User','GlobalMenu');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('time','javascript');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx', 'Cookie', 'AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	var $navis = array();
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * [ADMIN] 管理者ダッシュボードページを表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		/* 表示設定 */
		$this->subMenuElements = array("dashboard");
		$this->pageTitle = '管理者ダッシュボード';
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('Dblog', array('default' => $default));
		
		$this->paginate = array(
				'order' =>'Dblog.created DESC',
				'limit' => $this->passedArgs['num']
		);
		$this->set('viewDblogs',$this->paginate('Dblog'));

	}
/**
 * [ADMIN] 最近の動きを削除
 * 
 * @return void
 * @access public
 */
	function admin_del(){

		if($this->Dblog->deleteAll('1 = 1')){
			$this->Session->setFlash('最近の動きのログを削除しました。');
		} else {
			$this->Session->setFlash('最近の動きのログ削除に失敗しました。');
		}
		$this->redirect(array('action' => 'index'));

	}
	
}
?>