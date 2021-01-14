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

/**
 * Class MaintenanceController
 *
 * メンテナンスコントローラー
 *
 * @package Baser.Controller
 */
class MaintenanceController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Maintenance';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = null;

	/**
	 * ぱんくずナビ
	 *
	 * @var array
	 */
	public $crumbs = [];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * メンテナンス中ページを表示する
	 *
	 * @return void
	 * @access    public
	 */
	public function index()
	{
		$this->pageTitle = __d('baser', 'メンテナンス中');
		$this->response->statusCode(503);
	}

	/**
	 * [スマートフォン] メンテナンス中ページを表示する
	 *
	 * @return void
	 * @access public
	 */
	public function smartphone_index()
	{
		$this->setAction('index');
	}

}
