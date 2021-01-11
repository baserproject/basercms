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
 * ブログタグコントローラー
 *
 * @package Blog.Controller
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 * @property BlogTag $BlogTag
 */
class BlogTagsController extends BlogAppController
{

	/**
	 * クラス名
	 *
	 * @var array
	 */
	public $name = 'BlogTags';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Blog.BlogCategory', 'Blog.BlogTag'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents'];

	/**
	 * [ADMIN] タグ一覧
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num'], 'sort' => 'id', 'direction' => 'asc']];
		$this->setViewConditions('BlogTag', ['default' => $default]);

		$this->paginate = [
			'order' => 'BlogTag.id',
			'limit' => $this->passedArgs['num'],
			'recursive' => 0
		];
		$this->set('datas', $this->paginate('BlogTag'));

		$this->pageTitle = __d('baser', 'タグ一覧');
	}

	/**
	 * [ADMIN] タグ登録
	 *
	 * @return void
	 */
	public function admin_add()
	{
		if (!empty($this->request->data)) {
			$this->BlogTag->create($this->request->data);
			if ($this->BlogTag->save()) {
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'タグ「%s」を追加しました。'), $this->request->data['BlogTag']['name']));
				$this->redirect(['action' => 'index']);
			} else {
				$this->BcMessage->setError(__d('baser', 'エラーが発生しました。内容を確認してください。'));
			}
		}
		$this->pageTitle = __d('baser', '新規タグ登録');
		$this->render('form');
	}

	/**
	 * [ADMIN] タグ編集
	 *
	 * @param int $id タグID
	 * @return void
	 */
	public function admin_edit($id)
	{
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効な処理です。'));
			$this->redirect(['action' => 'index']);
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->BlogTag->read(null, $id);
		} else {
			$this->BlogTag->set($this->request->data);
			if ($this->BlogTag->save()) {
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'タグ「%s」を更新しました。'), $this->request->data['BlogTag']['name']));
				$this->redirect(['action' => 'index']);
			} else {
				$this->BcMessage->setError(__d('baser', 'エラーが発生しました。内容を確認してください。'));
			}
		}
		$this->pageTitle = __d('baser', 'タグ編集');
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除処理
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効な処理です。'));
			$this->redirect(['action' => 'index']);
		}

		$data = $this->BlogTag->read(null, $id);

		if ($this->BlogTag->delete($id)) {
			$this->BcMessage->setSuccess(sprintf(__d('baser', 'タグ「%s」を削除しました。'), $this->BlogTag->data['BlogTag']['name']));
		} else {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		}

		$this->redirect(['action' => 'index']);
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_delete($id = null)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		$data = $this->BlogTag->read(null, $id);
		if ($this->BlogTag->delete($id)) {
			$message = sprintf(__d('baser', 'タグ「%s」を削除しました。'), $this->BlogTag->data['BlogTag']['name']);
			$this->BlogTag->saveDbLog($message);
			exit(true);
		}
		exit();
	}

	/**
	 * [ADMIN] 一括削除
	 *
	 * @param int $id
	 * @return void
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				$data = $this->BlogTag->read(null, $id);
				if ($this->BlogTag->delete($id)) {
					$message = sprintf(__d('baser', 'タグ「%s」を削除しました。'), $this->BlogTag->data['BlogTag']['name']);
					$this->BlogTag->saveDbLog($message);
				}
			}
		}
		return true;
	}

	/**
	 * [ADMIN] AJAXタグ登録
	 *
	 * @return void
	 */
	public function admin_ajax_add()
	{
		if (!empty($this->request->data)) {
			$this->BlogTag->create($this->request->data);
			if ($data = $this->BlogTag->save()) {
				$result = [$this->BlogTag->id => $data['BlogTag']['name']];
				$this->set('result', $result);
			} else {
				$this->ajaxError(500, $this->BlogTag->validationErrors);
			}
		} else {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
	}

}
