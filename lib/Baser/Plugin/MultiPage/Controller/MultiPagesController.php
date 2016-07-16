<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiPage.Controller
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * MultiPagesController
 *
 * @package MultiPage.Controller
 * @property MultiPage $MultiPage
 */
class MultiPagesController extends AppController {

/**
 * コンポーネント
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('useForm' => true));

/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = array('multi_pages');

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BcAuth->allow('view');
	}

/**
 * コンテンツを追加する（AJAX）
 *
 * @return mixed false Or json
 */
	public function admin_add() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->request->data['MultiPage'] = array('content' => '本文が入ります。本文が入ります。本文が入ります。');
		if ($data = $this->MultiPage->save($this->request->data)) {
			$message = 'マルチページ「' . $this->request->data['Content']['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => $this->MultiPage->id,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		return false;
	}

/**
 * コンテンツを更新する
 *
 * @param $id
 * @return void
 */
	public function admin_edit($id) {
		$this->pageTitle = 'ページ編集';
		if(!$this->request->data) {
			$this->request->data = $this->MultiPage->read(null, $id);
			if(!$this->request->data) {
				$this->notFound();
			}
		} else {
			if ($this->MultiPage->save($this->request->data)) {
				$messege = 'マルチページ「' . $this->request->data['Content']['title'] . '」を更新しました。';
				$this->setMessage($messege, false, true);
				$this->redirect(array(
					'plugin'	=> 'multi_page',
					'controller'=> 'multi_pages',
					'action'	=> 'edit',
					$id
				));
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}
		}
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

/**
 * コピー
 *
 * @return bool
 */
	public function admin_copy() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$user = $this->BcAuth->user();
		if ($this->MultiPage->copy($this->request->data['entityId'], $this->request->data['title'], $user['id'], $this->request->data['siteId'])) {
			$message = 'マルチページのコピー「' . $this->request->data['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => $this->MultiPage->id,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		return false;
	}

/**
 * コンテンツ削除
 *
 * @param $id
 * @return bool
 */
	public function admin_delete() {
		if(empty($this->request->data['entityId'])) {
			return false;
		}
		if($this->MultiPage->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
	}

/**
 * コンテンツ表示
 *
 * @param $id
 * @return void
 */
	public function view ($id) {
		if(!$id) {
			$this->notFound();
		}
		$data = $this->MultiPage->find('first', array('conditions' => array('MultiPage.id' => $id)));
		if($this->BcContents->preview == 'default' && $this->request->data) {
			$data = $this->request->data;
		} elseif($this->BcContents->preview == 'alias') {
			$data['Content'] = $this->request->data['Content'];
		}
		if(!$data) {
			$this->notFound();
		}
		$this->set('data', $data);
		$this->set('editLink', array('plugin' => 'multi_page', 'admin' => true, 'controller' => 'multi_pages', 'action' => 'edit', $data['MultiPage']['id']));
	}

}