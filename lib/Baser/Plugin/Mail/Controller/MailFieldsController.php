<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メールフィールドコントローラー
 *
 * @package Mail.Controller
 * @property BcContentsComponent $BcContents
 * @property MailField $BcMailField
 * @property MailContent $MailContent
 * @property MailMessage $MailMessage
 */
class MailFieldsController extends MailAppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'MailFields';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('Mail.MailField', 'Mail.MailContent', 'Mail.MailMessage');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcHtml', 'BcTime', 'BcForm', 'BcText', 'BcCsv');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents' => ['type' => 'Mail.MailContent']);

/**
 * サブメニューエレメント
 *
 * @var string
 */
	public $subMenuElements = array();

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->MailContent->recursive = -1;
		$mailContentId = $this->params['pass'][0];
		$this->mailContent = $this->MailContent->read(null, $mailContentId);
		$this->request->params['Content'] = $this->BcContents->getContent($mailContentId)['Content'];
		$this->crumbs[] = array('name' => $this->request->params['Content']['title'] . '設定', 'url' => array('plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mailContentId));
		if($this->request->params['Content']['status']) {
			$this->set('publishLink', $this->request->params['Content']['url']);
		}
	}

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();
		$this->set('mailContent', $this->mailContent);
	}

/**
 * [ADMIN] メールフィールド一覧
 *
 * @param int $mailContentId
 * @return void
 */
	public function admin_index($mailContentId) {
		if (!$mailContentId || !$this->mailContent) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('controller' => 'mail_contents', 'action' => 'index'));
		}

		$conditions = $this->_createAdminIndexConditions($mailContentId);
		$datas = $this->MailField->find('all', array('conditions' => $conditions, 'order' => 'MailField.sort'));
		$this->set('datas', $datas);

		$this->_setAdminIndexViewData();

		if ($this->request->is('ajax') || !empty($this->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}
		$this->subMenuElements = array('mail_fields');
		$this->pageTitle = '[' . $this->request->params['Content']['title'] . '] メールフィールド一覧';
		$this->help = 'mail_fields_index';
	}

/**
 * 一覧の表示用データをセットする
 * 
 * @return void
 */
	protected function _setAdminIndexViewData() {
		/* セッション処理 */
		if (isset($this->params['named']['sortmode'])) {
			$this->Session->write('SortMode.MailField', $this->params['named']['sortmode']);
		}

		/* 並び替えモード */
		if (!$this->Session->check('SortMode.MailField')) {
			$this->set('sortmode', 0);
		} else {
			$this->set('sortmode', $this->Session->read('SortMode.MailField'));
		}
	}

/**
 * [ADMIN] メールフィールド追加
 *
 * @param int $mailContentId
 * @return void
 */
	public function admin_add($mailContentId) {
		if (!$mailContentId || !$this->mailContent) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('controller' => 'mail_contents', 'action' => 'index'));
		}

		if (!$this->request->data) {
			$this->request->data = $this->_getDefaultValue();
		} else {

			/* 登録処理 */
			$data = $this->request->data;
			if (is_array($data['MailField']['valid_ex'])) {
				$data['MailField']['valid_ex'] = implode(',', $data['MailField']['valid_ex']);
			}
			$data['MailField']['mail_content_id'] = $mailContentId;
			$data['MailField']['no'] = $this->MailField->getMax('no', array('MailField.mail_content_id' => $mailContentId)) + 1;
			$data['MailField']['sort'] = $this->MailField->getMax('sort') + 1;
			$this->MailField->create($data);
			if ($this->MailField->validates()) {
				if ($this->MailMessage->addMessageField($this->mailContent['MailContent']['id'], $data['MailField']['field_name'])) {
					// データを保存
					if ($this->MailField->save(null, false)) {
						$this->setMessage('新規メールフィールド「' . $data['MailField']['name'] . '」を追加しました。', false, true);
						$this->redirect(array('controller' => 'mail_fields', 'action' => 'index', $mailContentId));
					} else {
						$this->setMessage('データベース処理中にエラーが発生しました。', true);
					}
				} else {
					$this->setMessage('データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		$this->subMenuElements = array('mail_fields');
		$this->pageTitle = '[' . $this->request->params['Content']['title'] . '] 新規メールフィールド登録';
		$this->help = 'mail_fields_form';
		$this->render('form');
	}

/**
 * [ADMIN] 編集処理
 *
 * @param int $mailContentId
 * @param int $id
 * @return void
 */
	public function admin_edit($mailContentId, $id) {
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$data = $this->MailField->read(null, $id);
			$data['MailField']['valid_ex'] = explode(',', $data['MailField']['valid_ex']);
			$this->request->data = $data;
		} else {
			$old = $this->MailField->read(null, $id);
			$data = $this->request->data;
			if (is_array($data['MailField']['valid_ex'])) {
				$data['MailField']['valid_ex'] = implode(',', $data['MailField']['valid_ex']);
			}

			$this->MailField->set($data);
			if ($this->MailField->validates()) {
				$ret = true;
				if ($old['MailField']['field_name'] != $data['MailField']['field_name']) {
					$ret = $this->MailMessage->renameMessageField($mailContentId, $old['MailField']['field_name'], $data['MailField']['field_name']);
				}
				if ($ret) {
					/* 更新処理 */
					if ($this->MailField->save(null, false)) {
						$this->setMessage('メールフィールド「' . $data['MailField']['name'] . '」を更新しました。', false, true);
						$this->redirect(array('action' => 'index', $mailContentId));
					} else {
						$this->setMessage('データベース処理中にエラーが発生しました。', true);
					}
				} else {
					$this->setMessage('データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$this->subMenuElements = array('mail_fields');
		$this->pageTitle = '[' . $this->request->params['Content']['title'] . '] メールフィールド編集： ' . $this->request->data['MailField']['name'];
		$this->help = 'mail_fields_form';
		$this->render('form');
	}

/**
 * [ADMIN] 削除処理（Ajax）
 *
 * @param int $mailContentId
 * @param int $id
 * @return void
 */
	public function admin_ajax_delete($mailContentId, $id = null) {
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		// メッセージ用にデータを取得
		$mailField = $this->MailField->read(null, $id);

		/* 削除処理 */
		if ($mailField && $this->MailMessage->delMessageField($mailContentId, $mailField['MailField']['field_name'])) {
			if ($this->MailField->delete($id)) {
				$this->MailField->saveDbLog('メールフィールド「' . $mailField['MailField']['name'] . '」 を削除しました。');
				exit(true);
			}
		} else {
			$this->ajaxError(500, 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。');
		}
		exit();
	}

/**
 * [ADMIN] 削除処理
 *
 * @param int $mailContentId
 * @param int $id
 * @return void
 */
	public function admin_delete($mailContentId, $id = null) {
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'admin_index'));
		}

		// メッセージ用にデータを取得
		$mailField = $this->MailField->read(null, $id);

		/* 削除処理 */
		if ($this->MailMessage->delMessageField($mailContentId, $mailField['MailField']['field_name'])) {
			if ($this->MailField->delete($id)) {
				$this->setMessage('メールフィールド「' . $mailField['MailField']['name'] . '」 を削除しました。', false, true);
			} else {
				$this->setMessage('データベース処理中にエラーが発生しました。', true);
			}
		} else {
			$this->setMessage('データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。', true);
		}

		$this->redirect(array('action' => 'index', $mailContentId));
	}

/**
 * 一括削除
 * 
 * @param array $ids
 * @return boolean
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {

				// メッセージ用にデータを取得
				$mailField = $this->MailField->read(null, $id);
				/* 削除処理 */
				if ($this->MailMessage->delMessageField($mailField['MailField']['mail_content_id'], $mailField['MailField']['field_name'])) {
					if ($this->MailField->delete($id)) {
						$this->MailField->saveDbLog('メールフィールド「' . $mailField['MailField']['name'] . '」 を削除しました。');
					}
				}
			}
		}

		return true;
	}

/**
 * フォームの初期値を取得する
 *
 * @return string
 */
	protected function _getDefaultValue() {
		$data['MailField']['type'] = 'text';
		$data['MailField']['use_field'] = 1;
		$data['MailField']['no_send'] = 0;
		return $data;
	}

/**
 * フィールドデータをコピーする
 *
 * @param int $mailContentId
 * @param int $Id
 * @return void
 */
	public function admin_ajax_copy($mailContentId, $id) {
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id || !$mailContentId) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$result = $this->MailField->copy($id);
		if ($result) {
			$this->MailMessage->construction($mailContentId);
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。');
		}
	}

/**
 * メッセージCSVファイルをダウンロードする
 * 
 * @param int $mailContentId
 * @return void
 */
	public function admin_download_csv($mailContentId) {
		$mailContentId = (int) $mailContentId;
		if (!$mailContentId || !$this->mailContent || !is_int($mailContentId)) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('controller' => 'mail_contents', 'action' => 'index'));
		}
		$this->MailMessage->alias = 'MailMessage' . $mailContentId;
		$this->MailMessage->schema(true);
		$this->MailMessage->cacheSources = false;
		$this->MailMessage->setUseTable($mailContentId);
		$messages = $this->MailMessage->convertMessageToCsv($mailContentId, $this->MailMessage->find('all'));
		$this->set('encoding', $this->request->query['encoding']);
		$this->set('messages', $messages);
		$this->set('contentName', $this->request->params['Content']['name']);
	}

/**
 * 並び替えを更新する [AJAX]
 *
 * @param int $mailContentId
 * @return boolean
 * @access	public
 */
	public function admin_ajax_update_sort($mailContentId) {
		if (!$mailContentId) {
			$this->ajaxError(500, '無効な処理です。');
		}

		if ($this->request->data) {
			$conditions = $this->_createAdminIndexConditions($mailContentId);
			if ($this->MailField->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'], $conditions)) {
				exit(true);
			} else {
				$this->ajaxError(500, $this->MailField->validationErrors);
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}
		exit();
	}

/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param array $mailContentId
 * @return string
 */
	protected function _createAdminIndexConditions($mailContentId) {
		$conditions = array('MailField.mail_content_id' => $mailContentId);
		return $conditions;
	}

/**
 * [ADMIN] 無効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 */
	public function admin_ajax_unpublish($mailContentId, $id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->MailField->validationErrors);
		}
		exit();
	}

/**
 * [ADMIN] 有効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 */
	public function admin_ajax_publish($mailContentId, $id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->MailField->validationErrors);
		}
		exit();
	}

/**
 * 一括公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	protected function _batch_publish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_changeStatus($id, true);
			}
		}
		return true;
	}

/**
 * 一括非公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	protected function _batch_unpublish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_changeStatus($id, false);
			}
		}
		return true;
	}

/**
 * ステータスを変更する
 * 
 * @param int $id
 * @param boolean $status
 * @return boolean 
 */
	protected function _changeStatus($id, $status) {
		$statusTexts = array(0 => '無効', 1 => '有効');
		$data = $this->MailField->find('first', array('conditions' => array('MailField.id' => $id), 'recursive' => -1));
		$data['MailField']['use_field'] = $status;
		$this->MailField->set($data);

		if ($this->MailField->save()) {
			$statusText = $statusTexts[$status];
			$this->MailField->saveDbLog('メールフィールド「' . $data['MailField']['name'] . '」 を' . $statusText . '化しました。');
			return true;
		} else {
			return false;
		}
	}

}
