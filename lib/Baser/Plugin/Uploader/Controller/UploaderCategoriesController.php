<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Controller
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * ファイルカテゴリコントローラー
 *
 * @package         Uploader.Controller
 */
class UploaderCategoriesController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var        string
	 * @access    public
	 */
	public $name = 'UploaderCategories';
	/**
	 * モデル
	 *
	 * @var        array
	 * @access    public
	 */
	public $uses = ['Plugin', 'Uploader.UploaderCategory'];
	/**
	 * コンポーネント
	 *
	 * @var        array
	 * @access    public
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];
	/**
	 * サブメニュー
	 *
	 * @var        array
	 * @access    public
	 */
	public $subMenuElements = ['uploader'];

	/**
	 * ファイルカテゴリ一覧
	 *
	 * @return    void
	 * @access    public
	 */
	public function admin_index()
	{

		$this->pageTitle = __d('baser', 'カテゴリ一覧');
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('UploaderCategory', ['default' => $default]);
		$this->paginate = [
			'order' => 'UploaderCategory.id',
			'limit' => $this->passedArgs['num']
		];
		$this->set('datas', $this->paginate('UploaderCategory'));

	}

	/**
	 * 新規登録
	 *
	 * @return    void
	 * @access    public
	 */
	public function admin_add()
	{

		if ($this->request->data) {
			$this->UploaderCategory->set($this->request->data);
			if ($this->UploaderCategory->save()) {
				$message = sprintf(__d('baser', 'アップロードファイルカテゴリ「%s」を追加しました。'), $this->request->data['UploaderCategory']['name']);
				$this->BcMessage->setInfo($message);
				$this->UploaderCategory->saveDbLog($message);
				$this->redirect(['action' => 'index']);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}
		$this->pageTitle = __d('baser', 'カテゴリ新規登録');
		$this->render('form');

	}

	/**
	 * 編集
	 *
	 * @return    void
	 * @access    public
	 */
	public function admin_edit($id = null)
	{


		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->UploaderCategory->read(null, $id);
		} else {

			$this->UploaderCategory->set($this->request->data);
			if ($this->UploaderCategory->save()) {
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'アップロードファイルカテゴリ「%s」を更新しました。'), $this->request->data['UploaderCategory']['name']));
				$this->redirect(['action' => 'edit', $id]);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}

		}

		$this->pageTitle = __d('baser', 'カテゴリ編集');
		$this->render('form');

	}

	/**
	 * 削除
	 *
	 * @param int $id
	 * @return    void
	 * @access    public
	 */
	public function admin_delete($id = null)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		// メッセージ用にデータを取得
		$name = $this->UploaderCategory->field('name', ['UploaderCategory.id' => $id]);

		if ($this->UploaderCategory->delete($id)) {
			$this->BcMessage->setSuccess(sprintf(__d('baser', 'アップロードファイルカテゴリ「%s」を削除しました。'), $name));
		} else {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		}

		$this->redirect('index');

	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $uploaderCategoryId
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_delete($id = null)
	{
		$this->_checkSubmitToken();

		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// 削除実行
		if ($this->_del($id)) {
			clearViewCache();
			exit(true);
		} else {
			exit();
		}

	}

	/**
	 * 一括削除
	 *
	 * @param array $ids
	 * @return boolean
	 */
	public function _batch_del($ids)
	{

		if ($ids) {
			foreach($ids as $id) {
				$this->_del($id);
			}
		}
		return true;

	}

	/**
	 * データを削除する
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function _del($id = null)
	{
		// メッセージ用にデータを取得
		$data = $this->UploaderCategory->read(null, $id);
		// 削除実行
		if ($this->UploaderCategory->delete($id)) {
			$this->UploaderCategory->saveDbLog(sprintf(__d('baser', '%s を削除しました。'), $data['UploaderCategory']['name']));
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [ADMIN] コピー
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_copy($id = null)
	{
		$this->_checkSubmitToken();
		$result = $this->UploaderCategory->copy($id);
		if ($result) {
			$result['UploaderCategory']['id'] = $this->UploaderCategory->getInsertID();
			$this->setViewConditions('UploaderCategory', ['action' => 'admin_index']);
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->UploaderCategory->validationErrors);
		}

	}

}

