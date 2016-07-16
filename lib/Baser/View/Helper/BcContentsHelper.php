<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * 統合コンテンツ管理ヘルパ
 *
 * @package Baser.View.Helper
 */
class BcContentsHelper extends AppHelper {
	
/**
 * Constructor.
 *
 * @return	void
 * @access	public
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->settings = $this->_View->get('contentsSettings');
		foreach($this->settings as $key => $setting) {
			// icon
			if (!empty($setting['icon'])) {
				$this->settings[$key]['icon'] = $this->_getIconUrl($setting['plugin'], $setting['type'], $setting['icon']);
			} else {
				$this->settings[$key]['icon'] = $this->_getIconUrl($setting['plugin'], $setting['type'], null);
			}
		}
	}

/**
 * アイコンのURLを取得する
 * @param $type
 * @param $file
 * @param null $suffix
 * @return string
 */
	public function _getIconUrl ($plugin, $type, $file, $suffix = null) {
		$imageBaseUrl = Configure::read('App.imageBaseUrl');
		if($file) {
			if($plugin != 'Core') {
				$file = $plugin . '.' . $file;
			}
		} else {
			$icon = 'admin/icon_' . Inflector::underscore($type) . $suffix . '.png';
			$defaultIcon = 'admin/icon_content' . $suffix . '.png';
			if($plugin == 'Core') {
				$iconPath = WWW_ROOT . $imageBaseUrl . DS . $icon;
				if(file_exists($iconPath)) {
					$file = $icon;
				} else {
					$file = $defaultIcon;
				}
			} else {
				try {
					$pluginPath = CakePlugin::path($plugin) . 'webroot' . DS;
				}catch(Exception $e) {
					throw new ConfigureException('プラグインの BcContent 設定が間違っています。');
				}
				$iconPath = $pluginPath . str_replace('/', DS, $imageBaseUrl) . $icon;
				if(file_exists($iconPath)) {
					$file = $plugin . '.' . $icon;
				} else {
					$file = $defaultIcon;
				}
			}
		}
		return $this->assetUrl($file, array('pathPrefix' => $imageBaseUrl));
	}

/**
 * コンテンツ設定を Json 形式で取得する
 * @return string
 */
	public function getJsonSettings() {
		return json_encode($this->settings);
	}

/**
 * データが公開状態にあるか確認する
 *
 * @param $data
 * @return mixed
 */
	public function isAllowPublish($data) {
		$Content = ClassRegistry::init('Content');
		return $Content->allowPublish($data);
	}

/**
 * コンテンツIDよりフルURLを取得する
 *
 * @param $id
 * @return mixed
 */
	public function getUrlById($id, $full = false) {
		$Content = ClassRegistry::init('Content');
		return $Content->getUrlById($id, $full);
	}

/**
 * フルURLを取得する
 *
 * @param $url
 * @param bool $useSubDomain
 */
	public function getUrl($url, $full = false, $useSubDomain = false) {
		$Content = ClassRegistry::init('Content');
		return $Content->getUrl($url, $full, $useSubDomain);
	}

/**
 * プレフィックスなしのURLを取得する
 *
 * @param string $url
 * @param string $prefix
 * @param string $alias
 * @return mixed
 */
	public function getPureUrl($url, $prefix, $alias) {
		if($alias) {
			$prefix = $alias;
		}
		return preg_replace('/^\/' . preg_quote($prefix, '/') . '\//', '/', $url);
	}

/**
 * 現在のURLを元に指定したサブサイトのURLを取得する
 *
 * @param string $siteName
 * @return mixed|string
 */
	public function getCurrentRelatedSiteUrl($siteName) {
		if(empty($this->_View->site)) {
			return '';
		}
		$url = $this->getPureUrl('/' . $this->request->url, $this->_View->site['name'], $this->_View->site['alias']);
		$Site = ClassRegistry::init('Site');
		$site = $Site->find('first', ['conditions' => ['Site.name' => $siteName], 'recursive' => -1]);
		if(!$site) {
			return '';
		}
		$prefix = $Site->getPrefix($site);
		if($prefix) {
			$url = '/' . $prefix . $url;
		}
		return $url;
	}

}