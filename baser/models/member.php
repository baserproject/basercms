<?php
/* SVN FILE: $Id$ */
/**
 * メンバーモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * メンバーモデル
 *
 * @package baser.models
 */
class Member extends User {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Member';
/**
 * テーブル名
 *
 * @var string
 * @access public
 */
	var $useTable = 'users';
}