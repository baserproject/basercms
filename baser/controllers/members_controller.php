<?php
/* SVN FILE: $Id$ */
/**
 * メンバーコントローラー（デモ用）
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
App::import('Controller', 'Users');
/**
 * メンバーコントローラー（デモ用）
 *
 * @package baser.controllers
 */
class MembersController extends UsersController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Members';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Member', 'UserGroup');
/**
 * [MYPAGE] メンバー編集
 * 
 */
	function mypage_edit() {
		$this->pageTitle = 'メンバーマイページ（デモ）';
	}
}