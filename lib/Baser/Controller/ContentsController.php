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
 * 統合コンテンツ管理 コントローラー
 *
 * baserCMS内のコンテンツを統合的に管理する
 *
 * @package Baser.Controller
 * @property Content $Content
 */
class ContentsController extends AppController {

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['Content', 'Site'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array('useForm' => true));

/**
 * コンテンツ一覧
 *
 * @param integer $parentId
 * @param void
 */
	public function admin_index() {

		$this->pageTitle = 'コンテンツ一覧';
		$sites = $this->Site->getSiteList();
		$siteId = 0;
		if(!$sites) {
			$siteId = 1;
		}
		$default = array(
			'named' => array(
				'num'		=> $this->siteConfigs['admin_list_num'],
				'site_id'	=> $siteId
			)
		);
		$this->setViewConditions('Content', ['default' => $default]);
		if($this->action == 'admin_trash_index') {
			$this->pageTitle = 'ゴミ箱';
		}

		if (!empty($this->request->isAjax)) {
			$conditions = array();
			if($this->action == 'admin_index') {
				if($this->passedArgs['site_id'] != 'all') {
					$conditions = ['Content.site_id' => $this->passedArgs['site_id']];
				} else {
					$conditions = ['or' => [
						['Site.use_subdomain' => false],
						['Content.site_id' => 0]
					]];
				}
			} elseif($this->action == 'admin_trash_index') {
				$this->Content->Behaviors->unload('SoftDelete');
				$conditions = [
					'Content.deleted' => true
				];
			}
			$datas = $this->Content->find('threaded', ['order' => ['Content.site_id', 'Content.lft'], 'conditions' => $conditions, 'recursive' => 0]);
			$this->set('datas', $datas);
			Configure::write('debug', 0);
			$this->render('ajax_index');
			return;
		}
		$this->request->data['ViewSetting']['site_id'] = $this->passedArgs['site_id'];
		$this->set('sites', $sites);
		$this->subMenuElements = ['contents'];

	}

/**
 * ゴミ箱内のコンテンツ一覧を表示する
 */
	public function admin_trash_index() {
		$this->setAction('admin_index');
		if (empty($this->request->isAjax)) {
			$this->render('index');
		}
	}

/**
 * ゴミ箱のコンテンツを戻す
 *
 * @return mixed Site Id Or false
 */
	public function admin_ajax_trash_return() {
		if(empty($this->request->data['id'])) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->autoRender = false;
		$siteId = $this->Content->trashReturn($this->request->data['id']);
		if($siteId !== false) {
			return $siteId;
		}
		return false;
	}

/**
 * 新規コンテンツ登録（AJAX）
 *
 * @return void
 */
	public function admin_add($alias = false) {

		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$srcContent = array();
		if($alias) {
			if($this->request->data['Content']['alias_id']) {
				$conditions = array('id' => $this->request->data['Content']['alias_id']);
			} else {
				$conditions = array(
					'plugin' => $this->request->data['Content']['plugin'],
					'type' => $this->request->data['Content']['type']
				);
			}
			$srcContent = $this->Content->find('first', array('conditions' => $conditions, 'recursive' => -1));
			if($srcContent) {
				$this->request->data['Content']['alias_id'] = $srcContent['Content']['id'];
				$srcContent = $srcContent['Content'];
			}

			if(empty($this->request->data['Content']['parent_id']) && !empty($this->request->data['Content']['url'])) {
				$this->request->data['Content']['parent_id'] = $this->Content->copyContentFolderPath($this->request->data['Content']['url'], $this->request->data['Content']['site_id']);
			}

		}

		$user = $this->BcAuth->user();
		$this->request->data['Content']['author_id'] = $user['id'];
		$this->Content->create(false);
		if($data = $this->Content->save($this->request->data)) {
			if($alias) {
				$message = Configure::read('BcContents.items.' . $this->request->data['Content']['plugin'] . '.' . $this->request->data['Content']['type'] . '.title') .
					'「' . $srcContent['title'] . '」のエイリアス「' . $this->request->data['Content']['title'] . '」を追加しました。';
			} else {
				$message = Configure::read('BcContents.items.' . $this->request->data['Content']['plugin'] . '.' . $this->request->data['Content']['type'] . '.title') . '「' . $this->request->data['Content']['title'] . '」を追加しました。';
			}
			$this->setMessage($message, false, true, false);
			echo json_encode(array(
				'contentId' => $this->Content->id,
				'entityId' => null,
				'fullUrl' => $this->Content->getUrlById($this->Content->id, true)
			));
		} else {
			$this->ajaxError(500, '保存中にエラーが発生しました。');
		}
		exit();
	}

/**
 * コンテンツ編集
 *
 * @return void
 */
	public function admin_edit() {
		$this->pageTitle = 'コンテンツ編集';
		if(!$this->request->data) {
			$this->request->data = $this->Content->find('first', array('conditions' => array('Content.id' => $this->request->params['named']['content_id'])));
		} else {
			if($this->Content->save($this->request->data)) {
				$message = Configure::read('BcContents.items.' . $this->request->data['Content']['plugin'] . '.' . $this->request->data['Content']['type'] . '.title') . '「' . $this->request->data['Content']['title'] . '」を更新しました。';
				$this->setMessage($message, false, true);
				$this->redirect(array(
					'plugin'	=> null,
					'controller'=> 'contents',
					'action'	=> 'edit',
					'content_id' => $this->request->params['named']['content_id'],
					'parent_id' => $this->request->params['named']['parent_id']
				));
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}
		}
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

	/**
	 * エイリアスを編集する
	 *
	 * @param string $plugin
	 * @param string $type
	 */
	public function admin_edit_alias($id) {

		$this->pageTitle = 'エイリアス編集';
		if(!$this->request->data) {
			$this->request->data = $this->Content->find('first', array('conditions' => array('Content.id' => $id)));
			$srcContent = $this->Content->find('first', array('conditions' => array('Content.id' => $this->request->data['Content']['alias_id']), 'recursive' => -1));
			$srcContent = $srcContent['Content'];
		} else {
			if($this->Content->save($this->request->data)) {
				$srcContent = $this->Content->find('first', array('conditions' => array('Content.id' => $this->request->data['Content']['alias_id']), 'recursive' => -1));
				$srcContent = $srcContent['Content'];
				$message = Configure::read('BcContents.items.' . $srcContent['plugin'] . '.' . $srcContent['type'] . '.title') .
					'「' . $srcContent['title'] . '」のエイリアス「' . $this->request->data['Content']['title'] . '」を編集しました。';
				$this->setMessage($message, false, true);
				$this->redirect(array(
					'plugin'	=> null,
					'controller'=> 'contents',
					'action'	=> 'edit_alias',
					$id
				));
			} else {
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, true);
			}

		}

		$this->set('srcContent', $srcContent);
		$this->BcContents->settingForm($this, $this->request->data['Content']['site_id'], $this->request->data['Content']['id']);
		$this->set('publishLink', $this->request->data['Content']['url']);

	}

/**
 * コンテンツ削除
 *
 * @return void
 */
	public function admin_ajax_delete() {
		$this->autoRender = false;
		if(empty($this->request->data['contentId'])) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$content = $this->Content->find('first', array('conditions' => array('Content.id' => $this->request->data['contentId']), 'recursive' => -1));
		if(!$this->request->data['alias']) {
			$result = $this->Content->softDeleteFromTree($this->request->data['contentId']);
			$type = Configure::read('BcContents.items.' . $content['Content']['plugin'] . '.' . $content['Content']['type'] . '.title');
		} else {
			$this->Content->softDelete(false);
			$result = $this->Content->removeFromTree($this->request->data['contentId'], true);
			$this->Content->softDelete(true);
		}
		if($result) {
			$message = $type . '「' . $content['Content']['title'] . '」を削除しました。';
			$this->setMessage($message, false, true, false);
		} else {
			$this->ajaxError(500, '削除中にエラーが発生しました。');
		}
	}

/**
 * コンテンツ削除（論理削除）
 */
	public function admin_delete() {
		if(empty($this->request->data['Content']['id'])) {
			$this->notFound();
		}
		$content = $this->request->data;
		if(!$content['Content']['alias_id']) {
			$result = $this->Content->softDeleteFromTree($content['Content']['id']);
		} else {
			$this->Content->softDelete(false);
			$result = $this->Content->removeFromTree($content['Content']['id'], true);
			$this->Content->softDelete(true);
		}
		if($result) {
			$message = Configure::read('BcContents.items.' . $content['Content']['plugin'] . '.' . $content['Content']['type'] . '.title') . '「' . $content['Content']['title'] . '」を削除しました。';
			$this->setMessage($message, false, true);
			$this->redirect(array('plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index'));
		} else {
			$this->setMessage('削除中にエラーが発生しました。', true, true);
		}
	}

/**
 * ゴミ箱を空にする
 *
 * @return bool
 */
	public function admin_ajax_trash_empty() {
		$this->autoRender = false;
		$this->Content->softDelete(false);
		$contents = $this->Content->find('all', array('conditions' => array('Content.deleted'), 'order' => array('Content.plugin', 'Content.type'), 'recursive' => -1));
		$result = true;
		if($contents) {
			foreach($contents as $content) {
				$route = $this->BcContents->settings['items'][$content['Content']['type']]['routes']['delete'];
				if(!$this->requestAction($route, array('data' => array(
					'contentId' => $content['Content']['id'],
					'entityId' => $content['Content']['entity_id'],
				)))) {
					$result = false;
				}
			}
		}
		return $result;
	}

/**
 * コンテンツ表示
 *
 * @param $plugin
 * @param $type
 */
	public function view($plugin, $type) {
		$data = array('Content' => $this->request->params['Content']);
        if($this->BcContents->preview && $this->request->data) {
            $data = $this->request->data;
        }
		$this->set('data', $data);
		if(!$data['Content']['alias_id']) {
			$this->set('editLink', array('admin' => true, 'plugin' => '', 'controller' => 'contents', 'action' => 'edit', 'content_id' => $data['Content']['id']));
		} else {
			$this->set('editLink', array('admin' => true, 'plugin' => '', 'controller' => 'contents', 'action' => 'edit_alias', $data['Content']['id']));
		}
	}

/**
 * リネーム
 *
 * 新規登録時の初回リネーム時は、name にも保存する
 */
	public function admin_ajax_rename() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$data = ['Content' => [
			'id'		=> $this->request->data['id'],
			'title'		=> $this->request->data['newTitle'],
			'parent_id' => $this->request->data['parentId'],
			'type'		=> $this->request->data['type'],
			'site_id'	=> $this->request->data['siteId']
		]];
		if($this->Content->save($data, array('firstCreate' => !empty($this->request->data['first'])))) {
			$message = Configure::read('BcContents.items.' . $this->request->data['plugin'] . '.' . $this->request->data['type'] . '.title') .
						'「' . $this->request->data['oldTitle'] . '」を「' . $this->request->data['newTitle'] . '」に名称変更しました。';
			$this->setMessage($message, false, true, false);
			Configure::write('debug', 0);
			return $this->Content->getUrlById($this->Content->id);
		} else {
			$this->ajaxError(500, '名称変更中にエラーが発生しました。');
		}
		return false;
	}

/**
 * 並び順を移動する
 */
	public function admin_ajax_move() {

		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->Content->id = $this->request->data['currentId'];
		if (!$this->Content->exists()) {
			$this->ajaxError(500, 'データが存在しません。');
		}

		// EVENT Contents.beforeMove
		$event = $this->dispatchEvent('beforeMove', array(
			'data' => $this->request->data
		));
		if ($event !== false) {
			$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
		}
		
		$result = true;
		if($this->request->data['currentParentId'] == $this->request->data['targetParentId']) {
			$result = $this->Content->move($this->request->data['currentId'], $this->request->data['offset']);
		} else {
			// フォルダを絞って直下のデータを全件取得
			$conditions = array('Content.parent_id' => $this->request->data['targetParentId']);
			$contents = $this->Content->find('all', array(
				'fields' => array('Content.id', 'Content.parent_id', 'Content.title'),
				'order' => 'lft',
				'conditions' => $conditions,
				'recursive' => -1
			));
			$targetSort = null;
			if($contents) {
				$contents = Hash::extract($contents, '{n}.Content');
				// 移動先の並び順を取得
				foreach($contents as $key => $data) {
					if($this->request->data['targetId'] == $data['id']) {
						$targetSort = $key + 1;
						break;
					}
				}
			}
			// 親を変更
			$name = $this->Content->field('name', array('Content.id' => $this->request->data['currentId']));
			$result = $this->Content->save(array('Content' => array(
				'id'		=> $this->request->data['currentId'],
				'name'		=> $name,
				'parent_id' => $this->request->data['targetParentId'],
				'site_id'	=> $this->request->data['targetSiteId'],
				'type' 		=> $this->request->data['type'],
			)), false);
			if($targetSort && $result) {
				// 自分の並び順を取得
				$currentSort = count($contents) + 1;
				// 親変更後のオフセットを取得
				$offset = $targetSort - $currentSort;
				// オフセットを元に移動
				$result = $this->Content->move($this->request->data['currentId'], $offset);
			}
		}

		if($result) {

			// EVENT Contents.afterAdd
			$this->dispatchEvent('afterMove', array(
				'data' => $result
			));
			
			echo true;
		} else {
			$this->ajaxError(500, 'データ保存中にエラーが発生しました。');
		}
		exit();

	}

/**
 * 公開状態を変更する
 *
 * @return bool
 */
	public function admin_ajax_change_status() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->Content->id = $this->request->data['contentId'];
		if (!$this->Content->exists()) {
			$this->ajaxError(500, 'データが存在しません。');
		}
		$data = [];
		switch($this->request->data['status']) {
			case 'publish':
				$data = ['Content' => [
					'id' => $this->request->data['contentId'],
					'status' => true,
					'publish_begin' => '',
					'publish_end' => '',
					'type' => $this->request->data['type'],
					'site_id' => $this->request->data['siteId']
				]];
				break;
			case 'unpublish':
				$data = ['Content' => [
					'id' => $this->request->data['contentId'],
					'status' => false,
					'publish_begin' => '',
					'publish_end' => '',
					'type' => $this->request->data['type'],
					'site_id' => $this->request->data['siteId']
				]];
				break;
		}
		$result = true;
		$this->Content->getDataSource()->begin();
		if($data) {
			if(!$this->Content->save($data, false)) {
				$result = false;
			}
		} else {
			$result = false;
		}
		if($result) {
			$this->Content->getDataSource()->commit();
			return true;
		} else {
			$this->Content->getDataSource()->rollback();
			$this->ajaxError(500, 'データ保存中にエラーが発生しました。');
			return false;
		}
	}

/**
 * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
 *
 * @return mixed
 */
	public function admin_exists_content_by_url() {
		$this->autoRender = false;
		if(!$this->request->data['url']) {
			$this->ajaxError(500, '無効な処理です。');
		}
		Configure::write('debug', 0);
		return $this->Content->existsContentByUrl($this->request->data['url']);
	}

/**
 * 指定したIDのコンテンツが存在するか確認する
 * ゴミ箱のものは無視
 *
 * @param $id
 */
	public function admin_ajax_exists($id) {
		$this->autoRender = false;
		Configure::write('debug', 0);
		return $this->Content->exists($id);
	}

}