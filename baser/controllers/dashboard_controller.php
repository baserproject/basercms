<?php
/* SVN FILE: $Id$ */
/**
 * ダッシュボードコントローラー
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
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
 * ダッシュボードコントローラー
 *
 * 管理者ログインやメンバーログインのダッシュボードページを表示する
 *
 * @package			baser.controllers
 */
class DashboardController extends AppController{
/**
 * クラス名
 *
 * @var 	string
 * @access 	public
 */
	var $name = 'Dashboard';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Dblog','User','GlobalMenu');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('time','javascript');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth', 'Cookie', 'AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array();
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * [ADMIN] 管理者ダッシュボードページを表示する
 *
 * @return 	void
 * @access	public
 */
	function admin_index(){

		/* 表示設定 */
		$this->subMenuElements = array("dashboard");
		$this->pageTitle = '管理者ダッシュボード';
		$this->set('viewDblogs',$this->Dblog->findAll("","","created desc",12));
        
	}
/**
 * [MEMBER] メンバーダッシュボードページを表示する
 *
 * @return 	void
 * @access	public
 */
	function member_index(){

		/* 表示設定 */
		$this->subMenuElements = array('default');
		$this->controllerTitle = '';
		$this->pageTitle = 'メンバーダッシュボード';

	}

}
?>