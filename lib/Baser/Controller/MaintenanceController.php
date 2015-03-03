<?php

/**
 * メンテナンスコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メンテナンスコントローラー
 *
 * @package Baser.Controller
 */
class MaintenanceController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Maintenance';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = null;

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array();

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * メンテナンス中ページを表示する
 *
 * @return void
 * @access	public
 */
	public function index() {
		$this->pageTitle = 'メンテナンス中';
	}

/**
 * [モバイル] メンテナンス中ページを表示する
 *
 * @return void
 * @access	public
 */
	public function mobile_index() {
		$this->setAction('index');
	}

/**
 * [スマートフォン] メンテナンス中ページを表示する
 * 
 * @return void
 * @access public 
 */
	public function smartphone_index() {
		$this->setAction('index');
	}

}
