<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model.Behavior
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcCacheBehavior
 *
 * キャッシュビヘイビア
 *
 * @package Baser.Model.Behavior
 */
class BcCacheBehavior extends ModelBehavior
{

	/**
	 * setup
	 *
	 * @param Model $model
	 * @param array $config
	 * @return void
	 */
	public function setup(Model $model, $config = [])
	{
		if (!defined('CACHE_DATA_PATH')) {
			$setting = Cache::config('_cake_data_');
			if ($setting) {
				define('CACHE_DATA_PATH', $setting['settings']['path']);
			}
		}
		$this->createCacheFolder($model);
	}

	/**
	 * キャッシュフォルダーを生成する
	 *
	 * @param Model $model
	 */
	public function createCacheFolder(Model $model)
	{
		if (!defined('CACHE_DATA_PATH')) {
			return;
		}
		$path = CACHE_DATA_PATH . $model->tablePrefix . $model->table;
		if (!is_dir($path)) {
			mkdir($path);
			chmod($path, 0777);
		}
	}

	/**
	 * キャッシュ処理
	 *
	 * @param Model $model
	 * @param int $expire
	 * @param string $method
	 * @args mixed
	 * @return mixed
	 */
	public function readCache(Model $model, $expire, $type, $query = [])
	{
		static $cacheData = [];

		// キャッシュキー
		$tableName = $model->tablePrefix . $model->table;
		if (!isset($query['recursive'])) {
			$query['recursive'] = $model->recursive;
		}
		$cachekey = $tableName . '_' . $type . '_' . $expire . '_' . md5(serialize($query));

		// 変数キャッシュの場合
		if (!$expire) {
			if (isset($cacheData[$cachekey])) {
				return $cacheData[$cachekey];
			}
			if (!$db = ConnectionManager::getDataSource($model->useDbConfig)) {
				return false;
			}
			$results = $db->read($model, $query);
			$cacheData[$cachekey] = $results;
			return $results;
		}

		$this->changeCachePath($model->tablePrefix . $model->table);

		// サーバーキャッシュの場合
		$results = Cache::read($cachekey, '_cake_data_');

		if ($results !== false) {
			if ($results == "{false}") {
				$results = false;
			}
			return $results;
		}

		if (!$db = ConnectionManager::getDataSource($model->useDbConfig)) {
			return false;
		}
		$results = $db->read($model, $query);
		Cache::write($cachekey, ($results === false)? "{false}" : $results, '_cake_data_');

		return $results;
	}

	/**
	 * データキャッシュのパスを指定する
	 *
	 * @param string $dir
	 */
	public function changeCachePath($table)
	{
		if (!defined('CACHE_DATA_PATH')) {
			return;
		}
		$path = CACHE_DATA_PATH;
		$path .= $table . DS;
		Cache::config('_cake_data_', ['path' => $path]);
	}

	/**
	 * キャッシュを削除する
	 *
	 * @param Model $model
	 * @return void
	 */
	public function delCache(Model $model)
	{
		if (!defined('CACHE_DATA_PATH')) {
			return;
		}
		$path = CACHE_DATA_PATH . $model->tablePrefix . $model->table;
		$Folder = new Folder();
		$Folder->delete($path);
		$this->createCacheFolder($model);
	}

	/**
	 * afterSave
	 *
	 * @param Model $model
	 * @param boolean $created
	 * @return void
	 */
	public function afterSave(Model $model, $created, $options = [])
	{
		$this->delAssockCache($model);
	}

	/**
	 * afterDelete
	 *
	 * @param Model $model
	 * @return void
	 */
	public function afterDelete(Model $model)
	{
		$this->delAssockCache($model);
	}

	/**
	 * 関連モデルを含めてキャッシュを削除する
	 *
	 * @param Model $model
	 * @return void
	 * @todo 現在、3階層まで再帰対応。CakePHPのrecursiveの仕組み合わせたい
	 */
	public function delAssockCache(Model $model, $recursive = 0)
	{
		$this->delCache($model);
		if ($recursive <= 3) {
			$recursive++;
			$assocTypes = ['hasMany', 'hasOne', 'belongsTo', 'hasAndBelongsToMany'];
			foreach($assocTypes as $assocType) {
				if ($model->{$assocType}) {
					foreach($model->{$assocType} as $assoc) {
						$className = $assoc['className'];
						list($plugin, $className) = pluginSplit($className);
						if (isset($model->{$className})) {
							$this->delAssockCache($model->{$className}, $recursive);
						}
					}
				}
			}
		}
	}

}
