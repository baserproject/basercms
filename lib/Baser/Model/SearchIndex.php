<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class SearchIndex
 *
 * 検索インデックスモデル
 *
 * @package Baser.Model
 */
class SearchIndex extends AppModel
{

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
	public function reconstruct($parentContentId = null)
	{
		/* @var Content $contentModel */
		$contentModel = ClassRegistry::init('Content');
		$conditions = [
			'OR' => [
				['Site.status' => null],
				['Site.status' => true]
			]];
		if ($parentContentId) {
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
		$transactionBegun = false;
		if ($db->nestedTransactionSupported()) {
			$transactionBegun = $db->begin();
		}

		if (!$parentContentId) {
			$db->truncate('search_indices');
		}

		$result = true;
		if ($contents) {
			foreach($contents as $content) {
				if (isset($models[$content['Content']['type']])) {
					$modelClass = $models[$content['Content']['type']];
				} else {
					if (ClassRegistry::isKeySet($content['Content']['type'])) {
						$models[$content['Content']['type']] = $modelClass = ClassRegistry::getObject($content['Content']['type']);
					} else {
						if ($content['Content']['plugin'] == 'Core') {
							$modelName = $content['Content']['type'];
						} else {
							$modelName = $content['Content']['plugin'] . '.' . $content['Content']['type'];
						}
						$models[$content['Content']['type']] = $modelClass = ClassRegistry::init($modelName);
					}
				}
				$entity = $modelClass->find('first', ['conditions' => [$modelClass->name . '.id' => $content['Content']['entity_id']], 'recursive' => 0]);
				if (!$modelClass->save($entity, false)) {
					$result = false;
				}
			}
		}
		if ($transactionBegun) {
			if ($result) {
				$this->commit();
			} else {
				$this->roleback();
			}
		}
		return $result;
	}

	/**
	 * 公開状態確認
	 *
	 * @param array $data
	 * @return bool
	 */
	public function allowPublish($data)
	{
		if (isset($data['SearchIndex'])) {
			$data = $data['SearchIndex'];
		}
		$allowPublish = (int)$data['status'];
		if ($data['publish_begin'] == '0000-00-00 00:00:00') {
			$data['publish_begin'] = null;
		}
		if ($data['publish_end'] == '0000-00-00 00:00:00') {
			$data['publish_end'] = null;
		}
		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
			($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}
		return $allowPublish;
	}

}
