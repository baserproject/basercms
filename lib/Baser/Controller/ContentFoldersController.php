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
 */
class ContentFoldersController extends AppController {

/**
 * コンポーネント
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('useForm' => true));

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
		if ($this->ContentFolder->save($this->request->data)) {
			$data = array(
				'contentId'	=> $this->Content->id,
				'entityId'	=> $this->ContentFolder->id
			);
			$this->setMessage("フォルダ「{$this->request->data['Content']['title']}」を追加しました。", false, true, false);
			echo json_encode($data);
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
		} else {
			if ($this->ContentFolder->save($this->request->data)) {
				$this->setMessage("フォルダ「{$this->request->data['Content']['title']}」を更新しました。", false, true);
				$this->redirect(array(
					'plugin' => '',
					'controller' => 'content_folders',
					'action' => 'edit',
					$entityId
				));
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}
		}
		$contentTemplates = BcUtil::getTemplateList('ContentFolders', '', $this->siteConfigs['theme']);
		if($this->request->data['Content']['id'] != 1) {
			$parentTemplate = $this->getParentTemplate($this->request->data['Content']['id']);
			array_unshift($contentTemplates, array('' => '親フォルダの設定に従う（' . $parentTemplate . '）'));
		}
		$this->set('contentTemplates', $contentTemplates);
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

/**
 * 親のテンプレートを取得する
 *
 * @param $id
 */
	public function getParentTemplate($id) {
		$this->Content->bindModel(
			array('belongsTo' => array(
					'ContentFolder' => array(
						'className' => 'ContentFolder',
						'foreignKey' => 'entity_id'
					)
				)
			), false
		);
		$contents = $this->Content->getPath($id, null, 3);
		$this->Content->bindModel(array('belongsTo' => array('ContentFolder')));
		$contents = array_reverse($contents);
		unset($contents[0]);
		$parentTemplates = Hash::extract($contents, '{n}.Content.layout_template');
		$parentTemplate = '';
		foreach($parentTemplates as $parentTemplate) {
			if($parentTemplate) {
				break;
			}
		}
		return $parentTemplate;
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
		$data = $this->ContentFolder->find('first', array('conditions' => array('ContentFolder.id' => $entityId)));
		$this->ContentFolder->Content->Behaviors->Tree->settings['Content']['scope'] = array('Content.site_root' => false) + $this->ContentFolder->Content->getConditionAllowPublish();
		$children = $this->ContentFolder->Content->children($data['Content']['id'], true, array(), 'lft');
		if($this->BcContents->preview && !empty($this->request->data['Content'])) {
			$data['Content'] = $this->request->data['Content'];
		}
		$this->set(compact('data', 'children'));
		$contentTemplate = $data['ContentFolder']['content_template'];
		if(!$contentTemplate) {
			$contentTemplate = $this->getParentTemplate($data['Content']['id']);
			if(!$contentTemplate) {
				$contentTemplate = 'default';
			}
		}
		$this->set('editLink', array('admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $data['ContentFolder']['id'], 'content_id' => $data['Content']['id']));
		$this->render($contentTemplate);
	}

}