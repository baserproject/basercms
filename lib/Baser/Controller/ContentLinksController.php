<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcContentsController', 'Controller');

/**
 * Class ContentLinksController
 *
 * リンク コントローラー
 *
 * @package Baser.Controller
 */
class ContentLinksController extends AppController
{

	/**
	 * コンポーネント
	 * @var array
	 *
	 * @deprecated useViewCache 5.0.0 since 4.0.0
	 *    CakePHP3では、ビューキャッシュは廃止となる為、別の方法に移行する
	 */
	public $components = ['Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => ['useForm' => true, 'useViewCache' => true]];

	/**
	 * Before Filter
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BcAuth->allow('view');
	}

	/**
	 * コンテンツを登録する
	 *
	 * @return void
	 */
	public function admin_add()
	{
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$data = $this->ContentLink->save($this->request->data);
		if (!$data) {
			$this->ajaxError(500, __d('baser', '保存中にエラーが発生しました。'));
			exit;
		}

		$this->BcMessage->setSuccess(sprintf(__d('baser', 'リンク「%s」を追加しました。'), $this->request->data['Content']['title']), true, false);
		echo json_encode($data['Content']);
		exit();
	}

	/**
	 * コンテンツを更新する
	 *
	 * @return void
	 */
	public function admin_edit($entityId)
	{
		$this->pageTitle = __d('baser', 'リンク編集');
		if ($this->request->is(['post', 'put'])) {
			if ($this->ContentLink->isOverPostSize()) {
				$this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
				$this->redirect(['action' => 'edit', $entityId]);
			}
			if ($this->ContentLink->save($this->request->data)) {
				clearViewCache();
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'リンク「%s」を更新しました。'), $this->request->data['Content']['title']));
				$this->redirect([
					'plugin' => '',
					'controller' => 'content_links',
					'action' => 'edit',
					$entityId
				]);
			} else {
				$this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
			}
		} else {
			$this->request->data = $this->ContentLink->read(null, $entityId);
			if (!$this->request->data) {
				$this->BcMessage->setError(__d('baser', '無効な処理です。'));
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
			}
		}
		$this->set('publishLink', $this->Content->getPublishUrl($this->request->data['Content']));
	}

	/**
	 * コンテンツを削除する
	 *
	 * @return bool
	 */
	public function admin_delete()
	{
		if (empty($this->request->data['entityId'])) {
			return false;
		}
		if ($this->ContentLink->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
	}

	/**
	 * コンテンツを表示する
	 *
	 * @return void
	 */
	public function view()
	{
		if (empty($this->request->params['entityId'])) {
			$this->notFound();
		}
		$data = $this->ContentLink->find('first', ['conditions' => ['ContentLink.id' => $this->request->params['entityId']]]);
		if ($data) {
			$this->set(compact('data'));
		} else {
			$this->notFound();
		}
	}

}
