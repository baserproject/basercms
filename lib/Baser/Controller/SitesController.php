<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class SitesController
 *
 * サイトコントローラー
 *
 * @package Baser.Controller
 * @property Site $Site
 * @property BcManagerComponent $BcManager
 */
class SitesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = ['Cookie', 'BcAuth', 'BcAuthConfigure', 'BcManager'];

	/**
	 * サブメニュー
	 *
	 * @var array
	 */
	public $subMenuElements = ['site_configs', 'sites'];

	/**
	 * Before Filter
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->crumbs = [
			['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']],
			['name' => __d('baser', 'サブサイト管理'), 'url' => ['controller' => 'sites', 'action' => 'index']]
		];
	}

	/**
	 * サブサイト一覧
	 */
	public function admin_index()
	{
		$this->pageTitle = __d('baser', 'サブサイト一覧');
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
	public function admin_add()
	{

		if (!$this->request->data) {
			$this->request->data = ['Site' => [
				'title' => $this->siteConfigs['name'],
				'status' => false
			]];
		} else {
			/*** Sites.beforeAdd ** */
			$event = $this->dispatchEvent('beforeAdd', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true? $event->data['data'] : $event->result;
			}
			if ($data = $this->Site->save($this->request->data)) {
				/*** Sites.afterAdd ***/
				$this->dispatchEvent('afterAdd', [
					'data' => $data
				]);
				if (!empty($data['Site']['theme'])) {
					$this->BcManager->installThemesPlugins($data['Site']['theme']);
				}
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'サブサイト「%s」を追加しました。'), $this->request->data['Site']['name']));
				$this->redirect(['controller' => 'sites', 'action' => 'edit', $this->Site->id]);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}
		$this->pageTitle = __d('baser', 'サブサイト新規登録');
		$defaultThemeName = __d('baser', 'サイト基本設定に従う');
		if (!empty($this->siteConfigs['theme'])) {
			$defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
		}
		$themes = BcUtil::getThemeList();
		if (in_array($this->siteConfigs['theme'], $themes)) {
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
	public function admin_edit($id)
	{
		if (!$id) {
			$this->notFound();
		}
		if (!$this->request->data) {
			$this->request->data = $this->Site->find('first', ['conditions' => ['Site.id' => $id], 'recursive' => -1]);
			if (!$this->request->data) {
				$this->notFound();
			}
		} else {
			/*** Sites.beforeEdit ** */
			$event = $this->dispatchEvent('beforeEdit', [
				'data' => $this->request->data
			]);
			if ($event !== false) {
				$this->request->data = $event->result === true? $event->data['data'] : $event->result;
			}
			$beforeSite = $this->Site->find('first', ['conditions' => ['Site.id' => $this->request->data['Site']['id']]]);
			if ($data = $this->Site->save($this->request->data)) {
				/*** Sites.afterEdit ***/
				$this->dispatchEvent('afterEdit', [
					'data' => $data
				]);
				if (!empty($data['Site']['theme']) && $beforeSite['Site']['theme'] !== $data['Site']['theme']) {
					$this->BcManager->installThemesPlugins($data['Site']['theme']);
				}
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'サブサイト「%s」を更新しました。'), $this->request->data['Site']['name']));
				$this->redirect(['controller' => 'sites', 'action' => 'edit', $id]);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}
		$this->pageTitle = __d('baser', 'サブサイト編集');
		$defaultThemeName = __d('baser', 'サイト基本設定に従う');
		if (!empty($this->siteConfigs['theme'])) {
			$defaultThemeName .= '（' . $this->siteConfigs['theme'] . '）';
		}
		$themes = BcUtil::getThemeList();
		if (in_array($this->siteConfigs['theme'], $themes)) {
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
	public function admin_ajax_unpublish($id)
	{
		$this->_checkSubmitToken();
		$this->autoRender = false;
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		if (!$this->_changeStatus($id, false)) {
			$this->ajaxError(500, $this->Site->validationErrors);
			return false;
		}
		return true;
	}

	/**
	 * 非公開状態にする
	 *
	 * @param string $id
	 * @return bool
	 */
	public function admin_ajax_publish($id)
	{
		$this->_checkSubmitToken();
		$this->autoRender = false;
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		if (!$this->_changeStatus($id, true)) {
			$this->ajaxError(500, $this->Site->validationErrors);
			return false;
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
	protected function _changeStatus($id, $status)
	{
		$statusTexts = [0 => __d('baser', '非公開'), 1 => __d('baser', '公開')];
		$data = $this->Site->find('first', ['conditions' => ['Site.id' => $id], 'recursive' => -1]);
		$data['Site']['status'] = $status;
		if (!$this->Site->save($data)) {
			return false;
		}

		$statusText = $statusTexts[$status];
		$this->BcMessage->setSuccess(
			sprintf(
				__d('baser', 'サブサイト「%s」 を、%s に設定しました。'),
				$data['Site']['name'],
				$statusText
			),
			true,
			false
		);
		return true;
	}

	/**
	 * 削除する
	 */
	public function admin_delete()
	{
		if (empty($this->request->data['Site']['id'])) {
			$this->notFound();
		}
		if (!$this->Site->delete($this->request->data['Site']['id'])) {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
			$this->redirect(['action' => 'edit', $this->request->data['Site']['id']]);
			return;
		}
		$this->BcMessage->setSuccess(sprintf(__d('baser', 'サブサイト「%s」 を削除しました。'), $this->request->data['Site']['name']));
		$this->redirect(['action' => 'index']);
	}

	/**
	 * 選択可能なデバイスと言語の一覧を取得する
	 *
	 * @param int $mainSiteId メインサイトID
	 * @param int $currentSiteId 現在のサイトID
	 * @return string
	 */
	public function admin_ajax_get_selectable_devices_and_lang($mainSiteId, $currentSiteId = null)
	{
		$this->autoRender = false;
		Configure::write('debug', 0);
		return json_encode([
			'devices' => $this->Site->getSelectableDevices($mainSiteId, $currentSiteId),
			'langs' => $this->Site->getSelectableLangs($mainSiteId, $currentSiteId),
		]);
	}

}
