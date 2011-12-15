<?php
/* SVN FILE: $Id$ */
/**
 * ブログ設定コントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.controllers
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
 * ブログ設定コントローラー
 *
 * @package baser.plugins.blog.controllers
 */
class BlogConfigsController extends BlogAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogConfigs';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('User', 'Blog.BlogCategory', 'Blog.BlogConfig', 'Blog.BlogContent');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	var $navis = array('ブログ管理' => array('controller' => 'blog_contents', 'action' => 'index'));
/**
 * before_filter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		
		parent::beforeFilter();
		if($this->params['prefix']=='admin') {
			$this->subMenuElements = array('blog_common');
		}
		
	}
/**
 * [ADMIN] サイト基本設定
 *
 * @return void
 * @access public
 */
	function admin_form() {

		if(empty($this->data)) {
			$this->data = $this->BlogConfig->read(null, 1);
			$blogContentList = $this->BlogContent->find("list");
			$this->set('blogContentList',$blogContentList);
			$userList = $this->User->find("list");
			$this->set('userList',$userList);
		}else {

			/* 更新処理 */
			if($this->BlogConfig->save($this->data)) {
				$this->Session->setFlash('ブログ設定を保存しました。');
				$this->redirect(array('action' => 'form'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = 'ブログ設定';

	}
	
}
?>