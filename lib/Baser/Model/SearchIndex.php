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
 * 検索インデックスモデル
 *
 * @package Baser.Model
 */
class SearchIndex extends AppModel {

/**
 * クラス名
 * 
 * @var string
 */
	public $name = 'SearchIndex';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = ['BcCache'];

/**
 * 検索インデックス再構築
 * 
 * @param int $parentContentId 親となるコンテンツID
 * @return bool
 */
	public function reconstruct($parentContentId = null) {
		/* @var Content $contentModel */
		$contentModel = ClassRegistry::init('Content');
		$conditions = [
			'OR' => [
				['Site.status' => null],
				['Site.status' => true]
		]];
		if($parentContentId) {
			$parentContent = $contentModel->find('first', [
				'fields' => ['lft', 'rght'],
				'conditions' => ['id' => $parentContentId],
				'recursive' => -1
			]);
			$conditions = array_merge($conditions, [
				'lft >' => $parentContent['Content']['lft'], 
				'rght <' => $parentContent['Content']['rght']
			]);
		}
		$contents = $contentModel->find('all', [
			'conditions' => $conditions, 
			'order' => 'lft', 
			'recursive' => 2
		]);
		$models = [];
		$db = $this->getDataSource();
		$this->begin();
		
		if(!$parentContentId) {
			$db->truncate('search_indices');	
		}
		
		$result = true;
		if($contents) {
			foreach($contents as $content) {
				if(isset($models[$content['Content']['type']])) {
					$modelClass = $models[$content['Content']['type']];
				} else {
					if(ClassRegistry::isKeySet($content['Content']['type'])) {
						$models[$content['Content']['type']] = $modelClass = ClassRegistry::getObject($content['Content']['type']);
					} else {
						if($content['Content']['plugin'] == 'Core') {
							$modelName = $content['Content']['type'];
						} else {
							$modelName = $content['Content']['plugin'] . '.' . $content['Content']['type'];
						}
						$models[$content['Content']['type']] = $modelClass = ClassRegistry::init($modelName);
					}
				}
				$entity = $modelClass->find('first', ['conditions' => [$modelClass->name . '.id' => $content['Content']['entity_id']], 'recursive' => 0]);
				if(!$modelClass->save($entity, false)) {
					$result = false;
				}
			}
		}
		if($result) {
			$this->commit();
		} else {
			$this->roleback();
		}
		return $result;
	}

}
