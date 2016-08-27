<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 4.0.0
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
		if($this->save($data)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * フォルダのテンプレートリストを取得する
 *
 * @param $contentId
 * @param $theme
 * @return array
 */
	public function getFolderTemplateList($contentId, $theme) {
		$folderTemplates = BcUtil::getTemplateList('ContentFolders', '', $theme);
		if($contentId != 1) {
			$parentTemplate = $this->getParentTemplate($contentId, 'folder');
			$searchKey = array_search($parentTemplate, $folderTemplates);
			if($searchKey !== false) {
				unset($folderTemplates[$searchKey]);
			}
			array_unshift($folderTemplates, array('' => '親フォルダの設定に従う（' . $parentTemplate . '）'));
		}
		return $folderTemplates;
	}

/**
 * 親のテンプレートを取得する
 *
 * @param int $id
 * @param string $type folder|page
 */
	public function getParentTemplate($id, $type) {
		$this->Content->bindModel(
			array('belongsTo' => array(
					'ContentFolder' => array(
						'className' => 'ContentFolder',
						'foreignKey' => 'entity_id'
					)
				)
			)
		);
		$contents = $this->Content->getPath($id, null, 0);
		$contents = array_reverse($contents);
		unset($contents[0]);
		$parentTemplates = Hash::extract($contents, '{n}.ContentFolder.' . $type . '_template');
		$parentTemplate = '';
		foreach($parentTemplates as $parentTemplate) {
			if($parentTemplate) {
				break;
			}
		}
		if(!$parentTemplate) {
			$parentTemplate = 'default';
		}
		return $parentTemplate;
	}

}
