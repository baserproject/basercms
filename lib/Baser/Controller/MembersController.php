<?php

/**
 * メンバーコントローラー（デモ用）
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
 * Include files 
 */
App::uses('UsersController', 'Controller');

/**
 * メンバーコントローラー（デモ用）
 *
 * @package Baser.Controller
 */
class MembersController extends UsersController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Members';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Member', 'UserGroup');

/**
 * [MYPAGE] メンバー編集
 * 
 */
	public function mypage_index() {
		$this->pageTitle = 'メンバーマイページ';
	}

}
