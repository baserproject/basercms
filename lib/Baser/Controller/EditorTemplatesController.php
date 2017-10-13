<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * エディタテンプレートコントローラー
 * 
 * エディタテンプレートの管理を行う
 *
 * @package Baser.Controller
 */
class EditorTemplatesController extends AppController {

/**
 * コントローラー名
 * 
 * @var string
 */
	public $name = 'EditorTemplates';

/**
 * サブメニュー
 * 
 * @var array
 */
	public $subMenuElements = array('site_configs', 'editor_templates');

/**
 * パンくず設定
 * 
 * @var array
 */
	public $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
		array('name' => 'エディタテンプレート管理', 'url' => array('controller' => 'editor_templates', 'action' => 'index'))
	);

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if (!empty($this->siteConfigs['editor']) && $this->siteConfigs['editor'] != 'none') {
			$this->helpers[] = $this->siteConfigs['editor'];
		}
	}

/**
 * [ADMIN] 一覧 
 */
	public function admin_index() {
		$this->pageTitle = 'エディタテンプレート一覧';
		$this->help = 'editor_templates_index';

		$this->set('datas', $this->EditorTemplate->find('all'));
	}

/**
 * [ADMIN] 新規登録 
 */
	public function admin_add() {
		$this->pageTitle = 'エディタテンプレート新規登録';
		$this->help = 'editor_templates_form';

		if ($this->request->data) {
			$this->EditorTemplate->create($this->request->data);
			$result = $this->EditorTemplate->save();
			if ($result) {
				// EVENT EditorTemplates.afterAdd
				$this->dispatchEvent('afterAdd', array(
					'data' => $result
				));
				$this->setMessage('保存完了');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('保存中にエラーが発生しました。', true);
			}
		}
		$this->render('form');
	}

/**
 * [ADMIN] 編集
 * 
 * @param int $id 
 */
	public function admin_edit($id) {
		$this->pageTitle = 'エディタテンプレート編集';
		$this->help = 'editor_templates_form';

		if (!$this->request->data) {
			$this->request->data = $this->EditorTemplate->read(null, $id);
		} else {
			$this->EditorTemplate->set($this->request->data);
			$result = $this->EditorTemplate->save();
			if ($result) {
				// EVENT EditorTemplates.afterEdit
				$this->dispatchEvent('afterEdit', [
					'data' => $result
				]);
				$this->setMessage('保存完了');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('保存中にエラーが発生しました。', true);
			}
		}

		$this->render('form');
	}

/**
 * [ADMIN] 削除
 * 
 * @param int $id
 */
	public function admin_delete($id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}
		$data = $this->EditorTemplate->read(null, $id);
		if ($this->EditorTemplate->delete($id)) {
			$this->setMessage('エディタテンプレート「' . $data['EditorTemplate']['name'] . '」を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN AJAX] 削除
 * @param int $id 
 */
	public function admin_ajax_delete($id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$data = $this->EditorTemplate->read(null, $id);
		if ($this->EditorTemplate->delete($id)) {
			$this->EditorTemplate->saveDbLog('エディタテンプレート「' . $data['EditorTemplate']['name'] . '」を削除しました。');
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] CKEditor用テンプレート用のjavascriptを出力する 
 */
	public function admin_js() {
		header('Content-Type: text/javascript; name="editor_templates.js"');
		$this->layout = 'empty';
		$this->set('templates', $this->EditorTemplate->find('all'));
	}

}
