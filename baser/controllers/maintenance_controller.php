<?php
/* SVN FILE: $Id$ */
/**
 * メンテナンスコントローラー
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
 * メンテナンスコントローラー
 *
 * @package baser.controllers
 */
class MaintenanceController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Maintenance';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = null;
/**
 * ぱんくずナビ
 *
 * @var array
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
 * メンテナンス中ページを表示する
 *
 * @return void
 * @access	public
 */
	function index() {

		$this->pageTitle = 'メンテナンス中';

	}
/**
 * [モバイル] メンテナンス中ページを表示する
 *
 * @return void
 * @access	public
 */
	function mobile_index() {
		
		$this->setAction('index');
		
	}
/**
 * [スマートフォン] メンテナンス中ページを表示する
 * 
 * @return void
 * @access public 
 */
	function smartphone_index() {
		
		$this->setAction('index');
		
	}
}
