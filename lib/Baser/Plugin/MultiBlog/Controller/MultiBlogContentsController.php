<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.Controller
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * MultiBlogContentsController
 *
 * @package MultiBlog.Controller
 * @property MultiBlogContent $MultiBlogContent
 * @property Content $Content
 * @property CookieComponent $Cookie
 * @property BcAuthComponent $BcAuth
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 */
class MultiBlogContentsController extends AppController {

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('useForm' => true));

/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = array('multi_blog_contents');

/**
 * ブログを追加する
 *
 * @return json Or false
 */
	public function admin_add() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->request->data['MultiBlogContent'] = array('content' => 'ブログの説明文が入ります。');
		if ($data = $this->MultiBlogContent->save($this->request->data)) {
			$message = 'マルチブログ「' . $this->request->data['Content']['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => $this->MultiBlogContent->id,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		return false;
	}

/**
 * ブログを更新する
 *
 * @param int $id
 * @return void
 */
	public function admin_edit($id) {
		$this->pageTitle = 'マルチブログ編集';
		if(!$this->request->data) {
			$this->request->data = $this->MultiBlogContent->read(null, $id);
		} else {
			if ($this->MultiBlogContent->save($this->request->data)) {
				$messege = 'マルチブログ「' . $this->request->data['Content']['title'] . '」を更新しました。';
				$this->setMessage($messege, false, true);
				$this->redirect(array(
					'plugin' => 'multi_blog',
					'controller' => 'multi_blog_contents',
					'action' => 'edit',
					$id
				));
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}
		}
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

/**
 * ブログを削除する
 *
 * @param int $id
 * @return bool
 */
	public function admin_delete() {
		if(empty($this->request->data['entityId'])) {
			return false;
		}
		if($this->MultiBlogContent->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
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
		if ($this->MultiBlogContent->copy($this->request->data['entityId'], $this->request->data['title'], $user['id'], $this->request->data['siteId'])) {
			$message = 'マルチブログのコピー「' . $this->request->data['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => $this->MultiBlogContent->id,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		return false;
	}

}