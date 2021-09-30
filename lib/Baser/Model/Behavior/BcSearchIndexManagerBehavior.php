<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model.Behavior
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcSearchIndexManagerBehavior
 *
 * 検索インデックス管理ビヘイビア
 *
 * @package Baser.Model.Behavior
 */
class BcSearchIndexManagerBehavior extends ModelBehavior
{

	/**
	 * SearchIndex Model
	 *
	 * @var SearchIndex
	 */
	public $SearchIndex = null;

	/**
	 * 検索インデクスデータを登録する
	 *
	 * 検索インデクスデータを次のように作成して引き渡す
	 *
	 * ['SearchIndex' => [
	 *        'type' => 'コンテンツのタイプ',
	 *        'model_id' => 'モデルでのID',
	 *        'content_id' => 'コンテンツID',
	 *        'site_id' => 'サブサイトID',
	 *        'content_filter_id' => 'フィルターID' // カテゴリIDなど
	 *        'category' => 'カテゴリ名',
	 *        'title' => 'コンテンツタイトル', // 検索対象
	 *        'detail' => 'コンテンツ内容', // 検索対象
	 *        'url' => 'URL',
	 *        'status' => '公開ステータス',
	 *        'publish_begin' => '公開開始日',
	 *        'publish_end' => '公開終了日'
	 * ]]
	 *
	 * @param Model $model
	 * @param array $data
	 * @return bool
	 */
	public function saveSearchIndex(Model $model, $data)
	{
		if (!$data) {
			return false;
		}

		if (!empty($data['SearchIndex']['content_id'])) {
			$Content = ClassRegistry::init('Content');
			$content = $Content->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $data['SearchIndex']['content_id']], 'recursive' => 1]);
			$data['SearchIndex']['lft'] = $content['Content']['lft'];
			$data['SearchIndex']['rght'] = $content['Content']['rght'];
		} else {
			$data['SearchIndex']['lft'] = 0;
			$data['SearchIndex']['rght'] = 0;
		}
		$data['SearchIndex']['model'] = $model->alias;
		// タグ、空白を除外
		$data['SearchIndex']['detail'] = str_replace(["\r\n", "\r", "\n", "\t", "\s"], '', trim(strip_tags($data['SearchIndex']['detail'])));

		// MySQLの場合、検索テーブル'detail'では半角65535以上の文字数は保存できないため、オーバーした分はカットする。
		$datasources = ['csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL'];
		$db = ConnectionManager::getDataSource('default');
		list($type, $name) = explode('/', $db->config['datasource'], 2);
		$datasource = preg_replace('/^bc/', '', strtolower($name));
		if ($datasource == 'mysql' && mb_strlen($data['SearchIndex']['detail']) >= 21845) {
			$data['SearchIndex']['detail'] = mb_substr($data['SearchIndex']['detail'],0, 21844);
			$this->log(strlen($data['SearchIndex']['detail']));
		}

		// 検索用データとして保存
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
		$before = false;
		if (!empty($data['SearchIndex']['model_id'])) {
			$before = $this->SearchIndex->find('first', [
				'fields' => ['SearchIndex.id', 'SearchIndex.content_id'],
				'conditions' => [
					'SearchIndex.model' => $data['SearchIndex']['model'],
					'SearchIndex.model_id' => $data['SearchIndex']['model_id']
				]]);
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
			return $this->updateSearchIndexMeta($model);
		}

		return $result;
	}

	/**
	 * コンテンツデータを削除する
	 *
	 * @param Model $model
	 * @param string $id
	 */
	public function deleteSearchIndex(Model $model, $id)
	{
		$this->SearchIndex = ClassRegistry::init('SearchIndex');
		if ($this->SearchIndex->deleteAll(['SearchIndex.model' => $model->alias, 'SearchIndex.model_id' => $id])) {
			return $this->updateSearchIndexMeta($model);
		}
	}

	/**
	 * コンテンツメタ情報を更新する
	 *
	 * @param Model $model
	 * @return boolean
	 */
	public function updateSearchIndexMeta(Model $model)
	{
		$db = ConnectionManager::getDataSource('default');
		$contentTypes = [];
		$searchIndexes = $this->SearchIndex->find('all', ['fields' => ['SearchIndex.type'], 'group' => ['SearchIndex.type'], 'conditions' => ['SearchIndex.status' => true]]);
		foreach($searchIndexes as $searchIndex) {
			if ($searchIndex['SearchIndex']['type']) {
				$contentTypes[$searchIndex['SearchIndex']['type']] = $searchIndex['SearchIndex']['type'];
			}
		}
		$siteConfigs['SiteConfig']['content_types'] = BcUtil::serialize($contentTypes);
		$SiteConfig = ClassRegistry::init('SiteConfig');
		return $SiteConfig->saveKeyValue($siteConfigs);
	}

}
