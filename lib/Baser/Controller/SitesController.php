<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サイトコントローラー
 *
 * @package Baser.Controller
 */
class SitesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = ['Cookie', 'BcAuth', 'BcAuthConfigure'];

/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = ['sites'];

/**
 * サブサイト一覧
 */
	public function admin_index() {
		$this->pageTitle = 'サブサイト管理';
		$this->paginate = ['order' => 'id'];
		$datas = $this->paginate('Site');
		$this->set('mainSites', $this->Site->getSiteList());
		$this->set('datas', $datas);
	}

/**
 * サブサイト追加
 *
 * @param $id
 */
	public function admin_add() {

		if(!$this->request->data) {
			$this->request->data = ['Site' => [
				'title'	=> $this->siteConfigs['name'],
				'status' => false
			]];
		} else {
			/*** Sites.beforeAdd ** */
			$event = $this->dispatchEvent('beforeAdd', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
			}
			if($data = $this->Site->save($this->request->data)) {
				/*** Sites.afterAdd ***/
				$this->dispatchEvent('afterAdd', [
					'data' => $data
				]);
				$this->setMessage('サブサイト「' . $this->request->data['Site']['name'] . '」を追加しました。', false, true);
				$this->redirect(array('action' => 'edit', $this->Site->id));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}
		$this->pageTitle = 'サブサイト新規登録';
		$defaultThemeName = 'サイト基本設定に従う';
		if(!empty($this->siteConfigs['theme'])) {
			$defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
		}
		$this->set('mainSites', $this->Site->getSiteList(0));
		$this->set('themes', array_merge(['' => $defaultThemeName], BcUtil::getThemeList()));

	}

/**
 * サブサイト情報編集
 *
 * @param $id
 */
	public function admin_edit($id) {
		if(!$id) {
			$this->notFound();
		}
		if(!$this->request->data) {
			$this->request->data = $this->Site->find('first', ['conditions' => ['Site.id' => $id], 'recursive' => -1]);
			if(!$this->request->data) {
				$this->notFound();
			}
		} else {
			/*** Sites.beforeEdit ** */
			$event = $this->dispatchEvent('beforeEdit', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
			}
			if($data = $this->Site->save($this->request->data)) {
				/*** Sites.afterEdit ***/
				$this->dispatchEvent('afterEdit', [
					'data' => $data
				]);
				$this->setMessage('サブサイト「' . $this->request->data['Site']['name'] . '」を更新しました。', false, true);
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}
		$this->pageTitle = 'サブサイト編集';
		$defaultThemeName = 'サイト基本設定に従う';
		if(!empty($this->siteConfigs['theme'])) {
			$defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
		}
		$this->set('mainSites', $this->Site->getSiteList(0));
		$this->set('themes', array_merge(['' => $defaultThemeName], BcUtil::getThemeList()));
	}

/**
 * 公開状態にする
 *
 * @param string $id
 * @return bool
 */
	public function admin_ajax_unpublish($id) {
		$this->autoRender = false;
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			return true;
		} else {
			$this->ajaxError(500, $this->Site->validationErrors);
		}
		return false;
	}

/**
 * 非公開状態にする
 *
 * @param string $id
 * @return bool
 */
	public function admin_ajax_publish($id) {
		$this->autoRender = false;
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			return true;
		} else {
			$this->ajaxError(500, $this->Site->validationErrors);
		}
		return false;
	}

/**
 * ステータスを変更する
 *
 * @param int $id
 * @param boolean $status
 * @return boolean
 */
	protected function _changeStatus($id, $status) {
		$statusTexts = array(0 => '非公開', 1 => '公開');
		$data = $this->Site->find('first', array('conditions' => array('Site.id' => $id), 'recursive' => -1));
		$data['Site']['status'] = $status;
		if ($this->Site->save($data)) {
			$statusText = $statusTexts[$status];
			$this->setMessage('サブサイト「' . $data['Site']['name'] . '」 を' . $statusText . 'にしました。', false, true, false);
			return true;
		} else {
			return false;
		}
	}

/**
 * 削除する
 *
 * @param $id
 */
	public function admin_delete() {
		if(empty($this->request->data['Site']['id'])) {
			$this->notFound();
		}
		if($this->Site->delete($this->request->data['Site']['id'])) {
			$this->setMessage('サブサイト「' . $this->request->data['Site']['name'] . '」 を削除しました。', false, true);
			$this->redirect(['action' => 'index']);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
			$this->redirect(['action' => 'edit', $id]);
		}
	}

}