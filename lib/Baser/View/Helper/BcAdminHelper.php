<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * Include files
 */
App::uses('AppHelper', 'View/Helper');

/**
 * BcAdminヘルパー
 *
 * @package Baser.View.Helper
 */
class BcAdminHelper extends AppHelper {

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = ['BcBaser'];

/**
 * 管理システムグローバルメニューの利用可否確認
 * 
 * @return boolean
 */
	public function isAdminGlobalmenuUsed() {
		if (!BC_INSTALLED) {
			return false;
		}
		if (Configure::read('BcRequest.isUpdater')) {
			return false;
		}
		$user = $this->_View->get('user');
		if (!$user) {
			return false;
		}
		$UserGroup = ClassRegistry::init('UserGroup');
		return $UserGroup->isAdminGlobalmenuUsed($user['user_group_id']);
	}

/**
 * ログインユーザーがシステム管理者かチェックする
 * 
 * @return boolean 
 */
	public function isSystemAdmin() {
		$user = $this->_View->getVar('user');
		if (empty($this->request->params['admin']) || !$user) {
			return false;
		}
		if ($user['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			return true;
		}
		return false;
	}

	public function getJsonMenu() {
		$adminMenuGroups = Configure::read('BcApp.adminNavi');
		if(!$adminMenuGroups) {
			return null;
		}
		if(empty($this->_View->viewVars['user']['user_group_id'])) {
			return null;
		}
		$contents = $adminMenuGroups['Contents'];
		unset($adminMenuGroups['Contents']);
		$adminMenuGroups = $contents + $adminMenuGroups;
		$Permission = ClassRegistry::init('Permission');
		$covertedAdminMenuGroups = [];
		foreach($adminMenuGroups as $group => $adminMenuGroup) {
			if(!isset($adminMenuGroup['siteId'])) {
				$adminMenuGroup = array_merge(['siteId' => null], $adminMenuGroup);
			} else {
				$adminMenuGroup['siteId'] = (int) $adminMenuGroup['siteId'];
			}
			if(!isset($adminMenuGroup['type'])) {
				$adminMenuGroup = array_merge(['type' => null], $adminMenuGroup);
			}
			$adminMenuGroup = array_merge(['name' => $group], $adminMenuGroup);
			$covertedAdminMenus = [];
			if(!empty($adminMenuGroup['menus'])) {
				foreach($adminMenuGroup['menus'] as $menu => $adminMenu) {
					$adminMenu['name'] = $menu;
					$url = $this->BcBaser->getUrl($adminMenu['url']);
					$url = preg_replace('/^' . preg_quote($this->request->base, '/') . '\//', '/', $url);
					if ($Permission->check($url, $this->_View->viewVars['user']['user_group_id'])) {
						$adminMenu['url'] = $url;
						$covertedAdminMenus[] = $adminMenu;
					}
				}
			}
			$adminMenuGroup['menus'] = $covertedAdminMenus;
			$covertedAdminMenuGroups[] = $adminMenuGroup;
		}
		$menuSettings = [
			'currentSiteId' => null,
			'menuList' => $adminMenuGroups
		];
//		ini_set('xdebug.var_display_max_children', -1);
//		ini_set('xdebug.var_display_max_data', -1);
//		ini_set('xdebug.var_display_max_depth', -1);
//		var_dump($menuSettings);
		return json_encode($menuSettings);
	}

}
