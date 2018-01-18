<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログ設定コントローラー
 *
 * @package Blog.Controller
 */
class BlogConfigsController extends BlogAppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'BlogConfigs';

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['User', 'Blog.BlogCategory', 'Blog.BlogConfig', 'Blog.BlogContent'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = [];

/**
 * ぱんくずナビ
 *
 * @var string
 */
	public $crumbs = [
		['name' => 'プラグイン管理', 'url' => ['plugin' => '', 'controller' => 'plugins', 'action' => 'index']],
		['name' => 'ブログ管理', 'url' => ['controller' => 'blog_contents', 'action' => 'index']]
	];

/**
 * before_filter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if ($this->params['prefix'] == 'admin') {
			$this->subMenuElements = ['blog_common'];
		}
	}

/**
 * [ADMIN] サイト基本設定
 *
 * @return void
 */
//	public function admin_form() {
//		if (empty($this->request->data)) {
//			$this->request->data = $this->BlogConfig->read(null, 1);
//			$blogContentList = $this->BlogContent->find("list");
//			$this->set('blogContentList', $blogContentList);
//			$userList = $this->User->find("list");
//			$this->set('userList', $userList);
//		} else {
//
//			/* 更新処理 */
//			if ($this->BlogConfig->save($this->request->data)) {
//				$this->setMessage('ブログ設定を保存しました。', false, true);
//				$this->redirect(array('action' => 'form'));
//			} else {
//				$this->setMessage('入力エラーです。内容を修正してください。', true);
//			}
//		}
//
//		/* 表示設定 */
//		$this->pageTitle = 'ブログ設定';
//	}

}
