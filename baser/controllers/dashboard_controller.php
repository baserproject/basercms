<?php
/* SVN FILE: $Id$ */
/**
 * ダッシュボードコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
	var $uses = array('Dblog', 'User', 'GlobalMenu', 'Page');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_TIME_HELPER, 'Javascript');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	var $crumbs = array();
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * [ADMIN] 管理者ダッシュボードページにajaxでデータを取得する
 *
 * @return void
 * @access public
 */
	function admin_ajax_dblog_index() {

		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('Dblog', array('default' => $default, 'action' => 'admin_index'));
		$this->paginate = array(
				'order' => array('Dblog.created DESC', 'Dblog.id DESC'),
				'limit' => $this->passedArgs['num']
		);
		$this->set('viewDblogs',$this->paginate('Dblog'));
		
	}
/**
 * [ADMIN] 管理者ダッシュボードページを表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		$this->pageTitle = '管理者ダッシュボード';
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('Dblog', array('default' => $default));
		
		$this->paginate = array(
				'order' => array('Dblog.created DESC', 'Dblog.id DESC'),
				'limit' => $this->passedArgs['num']
		);
		
		$this->set('viewDblogs',$this->paginate('Dblog'));
		$publishedPages = $this->Page->find('count', array('conditions' => array('Page.status' => true)));
		$unpublishedPages = $this->Page->find('count', array('conditions' => array('Page.status' => false)));
		$totalPages = $publishedPages + $unpublishedPages;
		$this->set(compact('publishedPages', 'unpublishedPages', 'totalPages'));
		$this->help = 'dashboard_index';

	}
/**
 * [ADMIN] 最近の動きを削除
 * 
 * @return void
 * @access public
 */
	function admin_del(){

		if($this->Dblog->deleteAll('1 = 1')){
			$this->setMessage('最近の動きのログを削除しました。');
		} else {
			$this->setMessage('最近の動きのログ削除に失敗しました。', true);
		}
		$this->redirect(array('action' => 'index'));

	}
	
}
