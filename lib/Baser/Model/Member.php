<?php

/**
 * メンバーモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('User', 'Model');

/**
 * メンバーモデル
 *
 * @package Baser.Model
 */
class Member extends User {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Member';

/**
 * テーブル名
 *
 * @var string
 * @access public
 */
	public $useTable = 'users';

}
