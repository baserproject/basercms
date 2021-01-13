<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('UsersController', 'Controller');

/**
 * Class MembersController
 *
 * メンバーコントローラー（デモ用）
 *
 * @package Baser.Controller
 */
class MembersController extends UsersController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Members';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Member', 'UserGroup'];

	/**
	 * [MYPAGE] メンバー編集
	 *
	 */
	public function mypage_index()
	{
		$this->pageTitle = __d('baser', 'メンバーマイページ');
	}

}
