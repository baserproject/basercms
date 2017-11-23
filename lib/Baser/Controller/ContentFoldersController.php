<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcContentsController', 'Controller');

/**
 * フォルダ コントローラー
 *
 * @package Baser.Controller
 * @property ContentFolder $ContentFolder
 */
class ContentFoldersController extends AppController {

/**
 * コンポーネント
 * @var array
 * @deprecated useViewCache 5.0.0 since 4.0.0
 * 	CakePHP3では、ビューキャッシュは廃止となる為、別の方法に移行する
 */
	public $components = ['Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => ['useForm' => true, 'useViewCache' => true]];

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['ContentFolder', 'Page'];

/**
 * Before Filter
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BcAuth->allow('view');
	}
	
/**
 * コンテンツを登録する
 *
 * @return void
 */
	public function admin_add() {
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$data = $this->ContentFolder->save($this->request->data); 
		if ($data) {
			$this->setMessage("フォルダ「{$this->request->data['Content']['title']}」を追加しました。", false, true, false);
			echo json_encode($data['Content']);
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		exit();
	}

/**
 * コンテンツを更新する
 *
 * @return void
 */
	public function admin_edit($entityId) {
		$this->pageTitle = 'フォルダ編集';
		if(!$this->request->data) {
			$this->request->data = $this->ContentFolder->read(null, $entityId);
			if(!$this->request->data) {
				$this->setMessage('無効な処理です。', true);
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
			}
		} else {
			if ($this->ContentFolder->save($this->request->data)) {
				clearViewCache();
				$this->setMessage("フォルダ「{$this->request->data['Content']['title']}」を更新しました。", false, true);
				$this->redirect([
					'plugin' => '',
					'controller' => 'content_folders',
					'action' => 'edit',
					$entityId
				]);
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}
		}

		$theme = [$this->siteConfigs['theme']];
		$site = BcSite::findById($this->request->data['Content']['site_id']);
		if(!empty($site) && $site->theme && $site->theme != $this->siteConfigs['theme']) {
			$theme[] = $site->theme;
		}
		$this->set('folderTemplateList', $this->ContentFolder->getFolderTemplateList($this->request->data['Content']['id'], $theme));
		$this->set('pageTemplateList', $this->Page->getPageTemplateList($this->request->data['Content']['id'], $theme));
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

/**
 * コンテンツを削除する
 *
 * @param $entityId
 */
	public function admin_delete() {
		if(empty($this->request->data['entityId'])) {
			return false;
		}
		if($this->ContentFolder->delete($this->request->data['entityId'])) {
			return true;
		}
		return false;
	}

/**
 * コンテンツを表示する
 *
 * @param $entityId
 * @return void
 */
	public function view() {
		$entityId = $this->request->params['entityId'];
		$data = $this->ContentFolder->find('first', ['conditions' => ['ContentFolder.id' => $entityId]]);
		$this->ContentFolder->Content->Behaviors->Tree->settings['Content']['scope'] = ['Content.site_root' => false] + $this->ContentFolder->Content->getConditionAllowPublish();
		// 公開期間を条件に入れている為、キャッシュをオフにしないとキャッシュが無限増殖してしまう
		$this->ContentFolder->Content->Behaviors->unload('BcCache');
		$children = $this->ContentFolder->Content->children($data['Content']['id'], true, [], 'lft');
		$this->ContentFolder->Content->Behaviors->load('BcCache');
		$this->ContentFolder->Content->Behaviors->Tree->settings['Content']['scope'] = null;
		if($this->BcContents->preview && !empty($this->request->data['Content'])) {
			$data['Content'] = $this->request->data['Content'];
		}
		$this->set(compact('data', 'children'));
		$folderTemplate = $data['ContentFolder']['folder_template'];
		if(!$folderTemplate) {
			$folderTemplate = $this->ContentFolder->getParentTemplate($data['Content']['id'], 'folder');
		}
		$this->set('editLink', ['admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $data['ContentFolder']['id'], 'content_id' => $data['Content']['id']]);
		$this->render($folderTemplate);
	}

}