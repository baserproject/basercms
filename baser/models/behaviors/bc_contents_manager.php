<?php
/* SVN FILE: $Id$ */
/**
 * コンテンツ管理ビヘイビア
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.behaviors
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * コンテンツ管理ビヘイビア
 *
 * @subpackage baser.models.behaviors
 */
class BcContentsManagerBehavior extends ModelBehavior {
/**
 * Content Model
 * 
 * @var Content
 * @access public
 */
	var $Content = null;
/**
 * コンテンツデータを登録する
 * コンテンツデータを次のように作成して引き渡す
 * array('Content' =>
 *			array(	'model_id'	=> 'モデルでのID'
 *					'category'	=> 'カテゴリ名',
 *					'title'		=> 'コンテンツタイトル',		// 検索対象
 *					'detail'	=> 'コンテンツ内容',		// 検索対象
 *					'url'		=> 'URL',
 *					'status' => '公開ステータス'
 * ))
 *
 * @param Model $model
 * @param array $data
 * @return boolean
 * @access public
 */
	function saveContent($model, $data) {

		if(!$data) {
			return;
		}

		$data['Content']['model'] = $model->alias;
		// タグ、空白を除外
		$data['Content']['detail'] = str_replace(array("\r\n","\r","\n", "\t", "\s"), '', trim(strip_tags($data['Content']['detail'])));
		
		// 検索用データとして保存
		$id = '';
		$this->Content = ClassRegistry::init('Content');
		if(!empty($data['Content']['model_id'])) {
			$before = $this->Content->find('first', array(
				'fields' => array('Content.id', 'Content.category'),
				'conditions' => array(
					'Content.model' => $data['Content']['model'],
					'Content.model_id' => $data['Content']['model_id']
			)));
		}
		if($before) {
			$data['Content']['id'] = $before['Content']['id'];
			$this->Content->set($data);
		} else {
			if(empty($data['Content']['priority'])) {
				$data['Content']['priority'] = '0.5';
			}
			$this->Content->create($data);
		}
		$result = $this->Content->save();

		// カテゴリを site_configsに保存
		if($result) {
			return $this->updateContentMeta($model, $data['Content']['category']);
		}

		return $result;

	}
/**
 * コンテンツデータを削除する
 * 
 * @param Model $model
 * @param string $url 
 */
	function deleteContent($model, $id) {

		$this->Content = ClassRegistry::init('Content');
		if($this->Content->deleteAll(array('Content.model' => $model->alias, 'Content.model_id' => $id))) {
			return $this->updateContentMeta($model);
		}

	}
/**
 * コンテンツメタ情報を更新する
 *
 * @param string $contentCategory
 * @return boolean
 * @access public
 */
	function updateContentMeta($model) {
		
		$db = ConnectionManager::getDataSource('baser');
		$contentCategories = array();
		$contentTypes = array();
		if($db->config['driver']=='bc_csv') {
			// CSVの場合GROUP BYが利用できない（baserCMS 2.0.2）
			$contents = $this->Content->find('all', array('conditions' => array('Content.status' => true)));
			foreach($contents as $content) {
				if($content['Content']['category'] && !in_array($content['Content']['category'], $contentCategories)) {
					$contentCategories[$content['Content']['category']] = $content['Content']['category'];
				}
				if($content['Content']['type'] && !in_array($content['Content']['type'], $contentTypes)) {
					$contentTypes[$content['Content']['type']] = $content['Content']['type'];
				}
			}
		} else {
			$contents = $this->Content->find('all', array('fields' => array('Content.category'), 'group' => array('Content.category'), 'conditions' => array('Content.status' => true)));
			foreach($contents as $content) {
				if($content['Content']['category']) {
					$contentCategories[$content['Content']['category']] = $content['Content']['category'];
				}
			}
			$contents = $this->Content->find('all', array('fields' => array('Content.type'), 'group' => array('Content.type'), 'conditions' => array('Content.status' => true)));
			foreach($contents as $content) {
				if($content['Content']['type']) {
					$contentTypes[$content['Content']['type']] = $content['Content']['type'];
				}
			}
		}

		$siteConfigs['SiteConfig']['content_categories'] = serialize($contentCategories);
		$siteConfigs['SiteConfig']['content_types'] = serialize($contentTypes);
		$SiteConfig = ClassRegistry::init('SiteConfig');
		return $SiteConfig->saveKeyValue($siteConfigs);

	}

}
