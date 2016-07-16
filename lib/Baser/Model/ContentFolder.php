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
 * フォルダ モデル
 *
 * @package Baser.Model
 */

class ContentFolder extends AppModel {

/**
 * Behavior Setting
 *
 * @var array
 */
	public $actsAs = array('BcContents');

/**
 * サイトルートフォルダを保存
 *
 * @param null $siteId
 * @param array $data
 * @return bool
 */
	public function saveSiteRoot($siteId = null, $data = []) {
		if(!isset($data['Content'])) {
			$_data = $data;
			unset($data);
			$data['Content'] = $_data;
		}
		if(!is_null($siteId)) {
			$_data = $this->find('first', ['conditions' => [
				'Content.site_id' => $siteId,
				'Content.site_root' => true
			]]);
			$_data['Content'] = array_merge($_data['Content'], $data['Content']);
			$data = $_data;
		}
		if($data = $this->save($data, ['validate' => false])) {
			return true;
		} else {
			return false;
		}
	}
}
