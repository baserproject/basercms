<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
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
 */
	public $name = 'Member';

/**
 * テーブル名
 *
 * @var string
 */
	public $useTable = 'users';

}
