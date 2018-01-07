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

/**
 * サイトコントローラー
 *
 * @package Baser.Controller
 * @property Site $Site
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
	public $subMenuElements = ['site_configs', 'sites'];

/**
 * Before Filter
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->crumbs = [
			['name' => 'システム設定', 'url' => ['controller' => 'site_configs', 'action' => 'form']],
			['name' => 'サブサイト管理', 'url' => ['controller' => 'sites', 'action' => 'index']]
		];
	}
	
/**
 * サブサイト一覧
 */
	public function admin_index() {
		$this->pageTitle = 'サブサイト一覧';
		$this->paginate = ['order' => 'id'];
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('Site', ['default' => $default, 'action' => 'admin_index']);
		$this->paginate = [
			'order' => ['Site.id' => 'ASC'],
			'limit' => $this->passedArgs['num']
		];
		$datas = $this->paginate('Site');
		$this->set('mainSites', $this->Site->getSiteList());
		$this->set('datas', $datas);
	}

/**
 * サブサイト追加
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
				$this->redirect(['action' => 'edit', $this->Site->id]);
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}
		$this->pageTitle = 'サブサイト新規登録';
		$defaultThemeName = 'サイト基本設定に従う';
		if(!empty($this->siteConfigs['theme'])) {
			$defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
		}
		$themes = BcUtil::getThemeList();
		if(in_array($this->siteConfigs['theme'], $themes)) {
			unset($themes[$this->siteConfigs['theme']]);
		}
		$this->set('mainSites', $this->Site->getSiteList());
		$this->set('themes', array_merge(['' => $defaultThemeName], $themes));
		$this->help = 'sites_form';
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
				$this->redirect(['action' => 'edit', $id]);
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}
		$this->pageTitle = 'サブサイト編集';
		$defaultThemeName = 'サイト基本設定に従う';
		if(!empty($this->siteConfigs['theme'])) {
			$defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
		}
		$themes = BcUtil::getThemeList();
		if(in_array($this->siteConfigs['theme'], $themes)) {
			unset($themes[$this->siteConfigs['theme']]);
		}
		$this->set('mainSites', $this->Site->getSiteList(null, ['excludeIds' => $this->request->data['Site']['id']]));
		$this->set('themes', array_merge(['' => $defaultThemeName], $themes));
		$this->help = 'sites_form';
	}

/**
 * 公開状態にする
 *
 * @param string $id
 * @return bool
 */
	public function admin_ajax_unpublish($id) {
		$this->_checkSubmitToken();
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
		$this->_checkSubmitToken();
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
		$statusTexts = [0 => '非公開', 1 => '公開'];
		$data = $this->Site->find('first', ['conditions' => ['Site.id' => $id], 'recursive' => -1]);
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
			$this->redirect(['action' => 'edit', $this->request->data['Site']['id']]);
		}
	}

/**
 * 選択可能なデバイスと言語の一覧を取得する
 * 
 * @param int $mainSiteId メインサイトID
 * @param int $currentSiteId 現在のサイトID
 * @return string
 */
	public function admin_ajax_get_selectable_devices_and_lang($mainSiteId, $currentSiteId = null) {
		$this->autoRender = false;
		Configure::write('debug', 0);
		return json_encode([
			'devices' => $this->Site->getSelectableDevices($mainSiteId, $currentSiteId),
			'langs' => $this->Site->getSelectableLangs($mainSiteId, $currentSiteId),
		]);
	}

}