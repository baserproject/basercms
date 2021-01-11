<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('User', 'Model');

/**
 * Class Member
 *
 * メンバーモデル
 *
 * @package Baser.Model
 */
class Member extends User
{

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
