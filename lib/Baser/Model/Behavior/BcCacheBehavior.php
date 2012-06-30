<?php
/* SVN FILE: $Id$ */
/**
 * キャッシュビヘイビア
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.behaviors
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * キャッシュビヘイビア
 *
 * @subpackage		baser.models.behaviors
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
	public function setup(&$model, $config = array()) {}
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
	public function readCache(&$model, $expire, $type, $query = array()){
		
		static $cacheData = array();
		
		// キャッシュキー
		$tableName = $model->tablePrefix.$model->table;
		$cachekey = $tableName . '_' . $type . '_' . $expire . '_' . md5(serialize($query));
		
		// 変数キャッシュの場合
		if(!$expire){
			if (isset($cacheData[$cachekey])) {
				return $cacheData[$cachekey];
			}
			if (!$db =& ConnectionManager::getDataSource($model->useDbConfig)) {
				return false;
			}
			$results = $db->read($model, $query);
			$cacheData[$cachekey] = $results;
			return $results;
		}
		
		// サーバーキャッシュの場合
		$results = Cache::read($cachekey, '_cake_data_');
		if($results !== false){
			if($results == "{false}") {
				$results = false;
			}
			return $results;
		}
		
		if (!$db =& ConnectionManager::getDataSource($model->useDbConfig)) {
			return false;
		}
		$results = $db->read($model, $query);
		Cache::write($cachekey, ($results === false)? "{false}" : $results, '_cake_data_');
		// クリア用にモデル毎のキャッシュキーリストを作成
		$cacheListKey = $model->useTable . '_dataCacheList';
		$list = Cache::read($cacheListKey, '_cake_data_');
		$list[$cachekey] = 1;
		Cache::write($cacheListKey, $list, '_cake_data_');
		return $results;
		
	}
/**
 * キャッシュを削除する
 * 
 * @param Model $model
 * @return void
 * @access public
 */
	public function delCache(&$model){
		
		$cacheListKey = $model->useTable . '_dataCacheList';
		$list = Cache::read($cacheListKey, '_cake_data_');
		if(empty($list)) return;
		foreach($list as $key => $tmp){
			Cache::delete($key, '_cake_data_');
		}
		Cache::delete($cacheListKey, '_cake_data_');
		
	}
/**
 * afterSave
 * 
 * @param Model $model
 * @param boolean $created 
 * @return void
 * @access public
 */
	public function afterSave(&$model, $created) {
		
		$this->delAssockCache($model);
		
	}
/**
 * afterDelete
 * 
 * @param Model $model 
 * @return void
 * @access public
 */
	public function afterDelete(&$model) {
		
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
	public function delAssockCache(&$model, $recursive = 0) {
		
		$this->delCache($model);
		if($recursive <= 3) {
			$recursive++;
			$assocTypes = array('hasMany', 'hasOne', 'belongsTo', 'hasAndBelongsToMany');
			foreach($assocTypes as $assocType) {
				if($model->{$assocType}) {
					foreach($model->{$assocType} as $assoc) {
						$className = $assoc['className'];
						if(isset($model->{$className})) {
							$this->delAssockCache($model->{$className}, $recursive);
						}
					}
				}
			}
		}
		
	}
	
}