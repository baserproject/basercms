<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * カテゴリコントローラー
 *
 * @package Blog.Controller
 * @property BlogContent $BlogContent
 * @property BlogCategory $BlogCategory
 * @property BcContentsComponent $BcContents
 */
class BlogCategoriesController extends BlogAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'BlogCategories';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Blog.BlogCategory', 'Blog.BlogContent'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcText', 'BcTime', 'BcForm', 'Blog.Blog'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents' => ['type' => 'Blog.BlogContent']];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->BlogContent->recursive = -1;
		$content = $this->BcContents->getContent($this->request->params['pass'][0]);
		if (!$content) {
			$this->notFound();
		}
		$this->request->params['Content'] = $content['Content'];
		$this->request->params['Site'] = $content['Site'];
		$this->blogContent = $this->BlogContent->read(null, $this->params['pass'][0]);
		$this->crumbs[] = ['name' => sprintf(__d('baser', '%s 管理'), $this->request->params['Content']['title']), 'url' => ['controller' => 'blog_posts', 'action' => 'index', $this->params['pass'][0]]];

		if ($this->params['prefix'] == 'admin') {
			$this->subMenuElements = ['blog_posts'];
		}

		// バリデーション設定
		$this->BlogCategory->validationParams['blogContentId'] = $this->blogContent['BlogContent']['id'];
	}

	/**
	 * beforeRender
	 *
	 * @return void
	 */
	public function beforeRender()
	{
		parent::beforeRender();
		$this->set('blogContent', $this->blogContent);
	}

	/**
	 * [ADMIN] ブログを一覧表示する
	 *
	 * @return void
	 */
	public function admin_index($blogContentId)
	{
		$conditions = ['BlogCategory.blog_content_id' => $blogContentId];
		$_dbDatas = $this->BlogCategory->generateTreeList($conditions);
		$dbDatas = [];
		foreach($_dbDatas as $key => $dbData) {
			$category = $this->BlogCategory->find('first', ['conditions' => ['BlogCategory.id' => $key]]);
			if (preg_match("/^([_]+)/i", $dbData, $matches)) {
				$prefix = str_replace('_', '   ', $matches[1]);
				$category['BlogCategory']['title'] = $prefix . '└' . $category['BlogCategory']['title'];
				$category['BlogCategory']['depth'] = strlen($matches[1]);
			} else {
				$category['BlogCategory']['depth'] = 0;
			}
			$dbDatas[] = $category;
		}

		/* 表示設定 */
		$this->set('owners', $this->BlogCategory->getControlSource('owner_id'));
		$this->set('dbDatas', $dbDatas);
		$this->pageTitle = sprintf(__d('baser', '%s｜カテゴリ一覧'), $this->request->params['Content']['title']);
		$this->help = 'blog_categories_index';
	}

	/**
	 * [ADMIN] 登録処理
	 *
	 * @param string $blogContentId
	 * @return void
	 */
	public function admin_add($blogContentId)
	{
		if (!$blogContentId) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['controller' => 'blog_contents', 'action' => 'index']);
		}

		if (empty($this->request->data)) {

			$user = $this->BcAuth->user();
			$this->request->data = ['BlogCategory' => [
				'owner_id' => $user['user_group_id']
			]];
		} else {

			/* 登録処理 */
			$this->request->data['BlogCategory']['blog_content_id'] = $blogContentId;
			$this->request->data['BlogCategory']['no'] = $this->BlogCategory->getMax('no', ['BlogCategory.blog_content_id' => $blogContentId]) + 1;
			$this->BlogCategory->create($this->request->data);

			// データを保存
			if ($this->BlogCategory->save()) {
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'カテゴリー「%s」を追加しました。'), $this->request->data['BlogCategory']['name']));
				$this->redirect(['action' => 'index', $blogContentId]);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		/* 表示設定 */
		$user = $this->BcAuth->user();
		$catOptions = ['blogContentId' => $this->blogContent['BlogContent']['id']];
		if ($user['user_group_id'] != Configure::read('BcApp.adminGroupId')) {
			$catOptions['ownerId'] = $user['user_group_id'];
		}
		$parents = $this->BlogCategory->getControlSource('parent_id', $catOptions);
		if ($parents) {
			$parents = ['' => __d('baser', '指定しない')] + $parents;
		} else {
			$parents = ['' => __d('baser', '指定しない')];
		}
		$this->set('parents', $parents);
		$this->pageTitle = sprintf(__d('baser', '%s｜新規カテゴリ登録'), $this->request->params['Content']['title']);
		$this->help = 'blog_categories_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 編集処理
	 *
	 * @param int $blogContentId
	 * @param int $id
	 * @return void
	 */
	public function admin_edit($blogContentId, $id)
	{
		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->BlogCategory->read(null, $id);
		} else {

			/* 更新処理 */
			if ($this->BlogCategory->save($this->request->data)) {
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'カテゴリー「%s」を更新しました。'), $this->request->data['BlogCategory']['name']));
				$this->redirect(['action' => 'index', $blogContentId]);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		/* 表示設定 */
		$user = $this->BcAuth->user();
		$catOptions = [
			'blogContentId' => $this->blogContent['BlogContent']['id'],
			'excludeParentId' => $this->request->data['BlogCategory']['id']
		];
		if ($user['user_group_id'] != Configure::read('BcApp.adminGroupId')) {
			$catOptions['ownerId'] = $user['user_group_id'];
		}
		$parents = $this->BlogCategory->getControlSource('parent_id', $catOptions);
		if ($parents) {
			$parents = ['' => __d('baser', '指定しない')] + $parents;
		} else {
			$parents = ['' => __d('baser', '指定しない')];
		}

		if ($this->Content->isPublish($this->request->params['Content']['status'], $this->request->params['Content']['publish_begin'], $this->request->params['Content']['publish_end'])) {
			$this->set('publishLink', $this->Content->getUrl($this->request->params['Content']['url'] . 'archives/category/' . $this->request->data['BlogCategory']['name'], true, $this->request->params['Site']['use_subdomain']));
		}
		$this->set('parents', $parents);
		$this->pageTitle = sprintf(__d('baser', '%s｜カテゴリ編集'), $this->request->params['Content']['title']);
		$this->help = 'blog_categories_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 一括削除
	 *
	 * @param int $blogContentId
	 * @param int $id
	 * @return    void
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				$this->_del($id);
			}
		}
		return true;
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $blogContentId
	 * @param int $id
	 * @return    void
	 */
	public function admin_ajax_delete($blogContentId, $id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		if ($this->_del($id)) {
			exit(true);
		} else {
			exit();
		}
	}

	/**
	 * 削除処理
	 *
	 * @param int $blogContentId
	 * @param int $id
	 * @return    void
	 */
	protected function _del($id = null)
	{
		// メッセージ用にデータを取得
		$data = $this->BlogCategory->read(null, $id);
		/* 削除処理 */
		if ($this->BlogCategory->removeFromTreeRecursive($id)) {
			$this->BlogCategory->saveDbLog(sprintf(__d('baser', 'カテゴリー「%s」を削除しました。'), $data['BlogCategory']['name']));
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [ADMIN] 削除処理
	 *
	 * @param int $blogContentId
	 * @param int $id
	 * @return    void
	 */
	public function admin_delete($blogContentId, $id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		// メッセージ用にデータを取得
		$post = $this->BlogCategory->read(null, $id);

		/* 削除処理 */
		if ($this->BlogCategory->removeFromTreeRecursive($id)) {
			$this->BcMessage->setSuccess(sprintf(__d('baser', '%s を削除しました。'), $post['BlogCategory']['name']));
		} else {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		}

		$this->redirect(['action' => 'index', $blogContentId]);
	}

	/**
	 * [ADMIN] 追加処理（AJAX）
	 *
	 * @param int $blogContentId
	 */
	public function admin_ajax_add($blogContentId)
	{

		if (empty($this->request->data)) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			return;
		}

		// カテゴリ名が空の場合タイトルから取る
		if (empty($this->request->data['BlogCategory']['name'])) {
			$this->request->data['BlogCategory']['name'] = $this->request->data['BlogCategory']['title'];
		}

		// マルチバイトを含む場合はエンコードしておく
		if (strlen($this->request->data['BlogCategory']['name']) !== mb_strlen($this->request->data['BlogCategory']['name'])) {
			$this->request->data['BlogCategory']['name'] = substr(urlencode($this->request->data['BlogCategory']['name']), 0, 49);
		}

		$this->request->data['BlogCategory']['blog_content_id'] = $blogContentId;
		$this->request->data['BlogCategory']['no'] = $this->BlogCategory->getMax('no', ['BlogCategory.blog_content_id' => $blogContentId]) + 1;
		$this->BlogCategory->create($this->request->data);

		if (!$this->BlogCategory->save()) {
			$this->ajaxError(500, $this->BlogCategory->validationErrors);
		}

		echo $this->BlogCategory->getInsertID();
		exit();
	}

}
