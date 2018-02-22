<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Controller
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

/**
 * ファイルカテゴリコントローラー
 *
 * @package			Uploader.Controller
 */
class UploaderCategoriesController extends AppController {

/**
 * クラス名
 *
 * @var		string
 * @access	public
 */
	public $name = 'UploaderCategories';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	public $uses = array('Plugin', 'Uploader.UploaderCategory');
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	public $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * サブメニュー
 *
 * @var		array
 * @access	public
 */
	public $subMenuElements = array('uploader');
/**
 * ファイルカテゴリ一覧
 *
 * @return	void
 * @access	public
 */
	public function admin_index() {

		$this->pageTitle = __d('baser', 'カテゴリ一覧');
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('UploaderCategory', array('default' => $default));
		$this->paginate = array(
				'order'=>'UploaderCategory.id',
				'limit'=>$this->passedArgs['num']
		);
		$this->set('datas', $this->paginate('UploaderCategory'));

	}
/**
 * 新規登録
 *
 * @return	void
 * @access	public
 */
	public function admin_add() {

		if($this->request->data) {
			$this->UploaderCategory->set($this->request->data);
			if($this->UploaderCategory->save()) {
				$message = sprintf(__d('baser', 'アップロードファイルカテゴリ「%s」を追加しました。'), $this->request->data['UploaderCategory']['name']);
				$this->setMessage($message);
				$this->UploaderCategory->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else {
				$this->setMessage(__d('baser', '入力エラーです。内容を修正してください。'), true);
			}
		}
		$this->pageTitle = __d('baser', 'カテゴリ新規登録');
		$this->render('form');
		
	}
/**
 * 編集
 *
 * @return	void
 * @access	public
 */
	public function admin_edit($id = null) {


		/* 除外処理 */
		if(!$id && empty($this->request->data)) {
			$this->setMessage(__d('baser', '無効なIDです。'), true);
			$this->redirect(array('action'=>'index'));
		}

		if(empty($this->request->data)) {
			$this->request->data = $this->UploaderCategory->read(null, $id);
		}else {

			$this->UploaderCategory->set($this->request->data);
			if($this->UploaderCategory->save()) {
				$this->setMessage(sprintf(__d('baser', 'アップロードファイルカテゴリ「%s」を更新しました。'), $this->request->data['UploaderCategory']['name']), false, true);
				$this->redirect(array('action'=>'edit', $id));
			}else {
				$this->setMessage(__d('baser', '入力エラーです。内容を修正してください。'), true);
			}

		}

		$this->pageTitle = __d('baser', 'カテゴリ編集');
		$this->render('form');
		
	}
/**
 * 削除
 *
 * @param	int		$id
 * @return	void
 * @access	public
 */
	public function admin_delete($id = null) {
		$this->_checkSubmitToken();
		if(!$id) {
			$this->setMessage(__d('baser', '無効なIDです。'), true);
			$this->redirect(array('action'=>'index'));
		}

		// メッセージ用にデータを取得
		$name = $this->UploaderCategory->field('name', array('UploaderCategory.id' => $id));

		if($this->UploaderCategory->delete($id)) {
			$this->setMessage(sprintf(__d('baser', 'アップロードファイルカテゴリ「%s」を削除しました。'), $name), false, true);
		}else {
			$this->setMessage(__d('baser', 'データベース処理中にエラーが発生しました。'), true);
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
	public function admin_ajax_delete($id = null) {
		$this->_checkSubmitToken();

		if(!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// 削除実行
		if($this->_del($id)) {
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
	public function _batch_del($ids) {
		
		if($ids) {
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
	public function _del($id = null) {
		// メッセージ用にデータを取得
		$data = $this->UploaderCategory->read(null, $id);
		// 削除実行
		if($this->UploaderCategory->delete($id)) {
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
	public function admin_ajax_copy($id = null) {
		$this->_checkSubmitToken();
		$result = $this->UploaderCategory->copy($id);
		if($result) {
			$result['UploaderCategory']['id'] = $this->UploaderCategory->getInsertID();
			$this->setViewConditions('UploaderCategory', array('action' => 'admin_index'));
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->UploaderCategory->validationErrors);
		}
		
	}

}

