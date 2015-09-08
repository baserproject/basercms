<?php
/**
 * キャッシュビヘイビア
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Behavior
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * キャッシュビヘイビア
 *
 * @package		Baser.Model.Behavior
 */
class BcCacheBehavior extends ModelBehavior {

/**
 * setup
 * 
 * @param Model $model
 * @param array $config 
 * @return void
 * @access public
 */
	public function setup(Model $model, $config = array()) {
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
	public function createCacheFolder(Model $model) {
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
 * @access public
 */
	public function readCache(Model $model, $expire, $type, $query = array()) {
		static $cacheData = array();

		// キャッシュキー
		$tableName = $model->tablePrefix . $model->table;
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
		Cache::write($cachekey, ($results === false) ? "{false}" : $results, '_cake_data_');

		return $results;
	}

/**
 * データキャッシュのパスを指定する
 * 
 * @param string $dir 
 */
	public function changeCachePath($table) {
		if (!defined('CACHE_DATA_PATH')) {
			return;
		}
		$path = CACHE_DATA_PATH;
		$path .= $table . DS;
		Cache::config('_cake_data_', array('path' => $path));
	}

/**
 * キャッシュを削除する
 * 
 * @param Model $model
 * @return void
 * @access public
 */
	public function delCache(Model $model) {
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
 * @access public
 */
	public function afterSave(Model $model, $created, $options = array()) {
		$this->delAssockCache($model);
	}

/**
 * afterDelete
 * 
 * @param Model $model 
 * @return void
 * @access public
 */
	public function afterDelete(Model $model) {
		$this->delAssockCache($model);
	}

/**
 * 関連モデルを含めてキャッシュを削除する
 * 
 * @param Model $model
 * @return void
 * @access public
 * @todo 現在、3階層まで再帰対応。CakePHPのrecursiveの仕組み合わせたい
 */
	public function delAssockCache(Model $model, $recursive = 0) {
		$this->delCache($model);
		if ($recursive <= 3) {
			$recursive++;
			$assocTypes = array('hasMany', 'hasOne', 'belongsTo', 'hasAndBelongsToMany');
			foreach ($assocTypes as $assocType) {
				if ($model->{$assocType}) {
					foreach ($model->{$assocType} as $assoc) {
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
