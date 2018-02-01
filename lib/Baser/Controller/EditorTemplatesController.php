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
	public $subMenuElements = ['site_configs', 'editor_templates'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->crumbs = [
			['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']],
			['name' => __d('baser', 'エディタテンプレート管理'), 'url' => ['controller' => 'editor_templates', 'action' => 'index']]
		];
		if (!empty($this->siteConfigs['editor']) && $this->siteConfigs['editor'] != 'none') {
			$this->helpers[] = $this->siteConfigs['editor'];
		}
	}

/**
 * [ADMIN] 一覧 
 */
	public function admin_index() {
		$this->pageTitle = __d('baser', 'エディタテンプレート一覧');
		$this->help = 'editor_templates_index';

		$this->set('datas', $this->EditorTemplate->find('all'));
	}

/**
 * [ADMIN] 新規登録 
 */
	public function admin_add() {
		$this->pageTitle = __d('baser', 'エディタテンプレート新規登録');
		$this->help = 'editor_templates_form';

		if ($this->request->data) {
			$this->EditorTemplate->create($this->request->data);
			$result = $this->EditorTemplate->save();
			if ($result) {
				// EVENT EditorTemplates.afterAdd
				$this->dispatchEvent('afterAdd', [
					'data' => $result
				]);
				$this->setMessage(__d('baser', '保存完了'));
				$this->redirect(['action' => 'index']);
			} else {
				$this->setMessage(__d('baser', '保存中にエラーが発生しました。'), true);
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
		$this->pageTitle = __d('baser', 'エディタテンプレート編集');
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
				$this->setMessage(__d('baser', '保存完了'));
				$this->redirect(['action' => 'index']);
			} else {
				$this->setMessage(__d('baser', '保存中にエラーが発生しました。'), true);
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
			$this->setMessage(__d('baser', '無効なIDです。'), true);
			$this->redirect(['action' => 'index']);
		}
		$data = $this->EditorTemplate->read(null, $id);
		if ($this->EditorTemplate->delete($id)) {
			$this->setMessage(sprintf(__d('baser', 'エディタテンプレート「%s」を削除しました。'), $data['EditorTemplate']['name']), false, true);
		} else {
			$this->setMessage(__d('baser', 'データベース処理中にエラーが発生しました。'), true);
		}
		$this->redirect(['action' => 'index']);
	}

/**
 * [ADMIN AJAX] 削除
 * @param int $id 
 */
	public function admin_ajax_delete($id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$data = $this->EditorTemplate->read(null, $id);
		if ($this->EditorTemplate->delete($id)) {
			$this->EditorTemplate->saveDbLog(sprintf(__d('baser', 'エディタテンプレート「%s」を削除しました。'), $data['EditorTemplate']['name']));
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
