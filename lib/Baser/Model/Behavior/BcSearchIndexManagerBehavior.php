<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Behavior
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * 検索インデックス管理ビヘイビア
 *
 * @package Baser.Model.Behavior
 */
class BcSearchIndexManagerBehavior extends ModelBehavior {

/**
 * SearchIndex Model
 * 
 * @var SearchIndex
 */
	public $SearchIndex = null;

/**
 * コンテンツデータを登録する
 * コンテンツデータを次のように作成して引き渡す
 * array('SearchIndex' =>
 * 			array(	'model_id'	=> 'モデルでのID'
 * 					'category'	=> 'カテゴリ名',
 * 					'title'		=> 'コンテンツタイトル',		// 検索対象
 * 					'detail'	=> 'コンテンツ内容',		// 検索対象
 * 					'url'		=> 'URL',
 * 					'status' => '公開ステータス'
 * ))
 *
 * @param Model $model
 * @param array $data
 * @return boolean
 */
	public function saveSearchIndex(Model $model, $data) {
		if (!$data) {
			return;
		}

		$data['SearchIndex']['model'] = $model->alias;
		// タグ、空白を除外
		$data['SearchIndex']['detail'] = str_replace(array("\r\n", "\r", "\n", "\t", "\s"), '', trim(strip_tags($data['SearchIndex']['detail'])));

		// 検索用データとして保存
		$id = '';
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
		if (!empty($data['SearchIndex']['model_id'])) {
			$before = $this->SearchIndex->find('first', array(
				'fields' => array('SearchIndex.id', 'SearchIndex.category'),
				'conditions' => array(
					'SearchIndex.model' => $data['SearchIndex']['model'],
					'SearchIndex.model_id' => $data['SearchIndex']['model_id']
			)));
		}
		if ($before) {
			$data['SearchIndex']['id'] = $before['SearchIndex']['id'];
			$this->SearchIndex->set($data);
		} else {
			if (empty($data['SearchIndex']['priority'])) {
				$data['SearchIndex']['priority'] = '0.5';
			}
			$this->SearchIndex->create($data);
		}
		$result = $this->SearchIndex->save();

		// カテゴリを site_configsに保存
		if ($result) {
			return $this->updateSearchIndexMeta($model, $data['SearchIndex']['category']);
		}

		return $result;
	}

/**
 * コンテンツデータを削除する
 * 
 * @param Model $model
 * @param string $id
 */
	public function deleteSearchIndex(Model $model, $id) {
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
		if ($this->SearchIndex->deleteAll(array('SearchIndex.model' => $model->alias, 'SearchIndex.model_id' => $id))) {
			return $this->updateSearchIndexMeta($model);
		}
	}

/**
 * コンテンツメタ情報を更新する
 *
 * @param Model $model
 * @return boolean
 */
	public function updateSearchIndexMeta(Model $model) {
		$db = ConnectionManager::getDataSource('baser');
		$contentCategories = array();
		$contentTypes = array();
		if ($db->config['datasource'] == 'Database/BcCsv') {
			// CSVの場合GROUP BYが利用できない（baserCMS 2.0.2）
			$contents = $this->SearchIndex->find('all', array('conditions' => array('SearchIndex.status' => true)));
			foreach ($contents as $content) {
				if ($content['SearchIndex']['category'] && !in_array($content['SearchIndex']['category'], $contentCategories)) {
					$contentCategories[$content['SearchIndex']['category']] = $content['SearchIndex']['category'];
				}
				if ($content['SearchIndex']['type'] && !in_array($content['SearchIndex']['type'], $contentTypes)) {
					$contentTypes[$content['SearchIndex']['type']] = $content['SearchIndex']['type'];
				}
			}
		} else {
			$searchIndexes = $this->SearchIndex->find('all', array('fields' => array('SearchIndex.category'), 'group' => array('SearchIndex.category'), 'conditions' => array('SearchIndex.status' => true)));
			foreach ($searchIndexes as $searchIndex) {
				if ($searchIndex['SearchIndex']['category']) {
					$contentCategories[$searchIndex['SearchIndex']['category']] = $searchIndex['SearchIndex']['category'];
				}
			}
			$searchIndexes = $this->SearchIndex->find('all', array('fields' => array('SearchIndex.type'), 'group' => array('SearchIndex.type'), 'conditions' => array('SearchIndex.status' => true)));
			foreach ($searchIndexes as $searchIndex) {
				if ($searchIndex['SearchIndex']['type']) {
					$contentTypes[$searchIndex['SearchIndex']['type']] = $searchIndex['SearchIndex']['type'];
				}
			}
		}

		$siteConfigs['SiteConfig']['content_categories'] = BcUtil::serialize($contentCategories);
		$siteConfigs['SiteConfig']['content_types'] = BcUtil::serialize($contentTypes);
		$SiteConfig = ClassRegistry::init('SiteConfig');
		return $SiteConfig->saveKeyValue($siteConfigs);
	}

}
