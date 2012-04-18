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
class CacheBehavior extends ModelBehavior {
/**
 * 利用状態
 * 
 * @var boolean
 * @access public
 */
	var $enabled = true;
/**
 * setup
 * 
 * @param Model $model
 * @param array $config 
 * @return void
 * @access public
 */
	function setup(&$model, $config = array()) {}
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
	function cacheMethod(&$model, $expire, $method, $args = array()){
		
		static $cacheData = array();
		$this->enabled = false;
		// キャッシュキー
		$cachekey = get_class($model) . '_' . $method . '_'  . $expire . '_' . md5(serialize($args));
		// 変数キャッシュの場合
		if(!$expire){
			if (isset($cacheData[$cachekey])) {
				$this->enabled = true;
				return $cacheData[$cachekey];
			}
			$ret = call_user_func_array(array($model, $method), $args);
			$this->enabled = true;
			$cacheData[$cachekey] = $ret;
			return $ret;
		}
		// サーバーキャッシュの場合
		$ret = Cache::read($cachekey, '_cake_data_');
		if($ret !== false){
			$this->enabled = true;
			if($ret == "{false}") {
				$ret = false;
			}
			return $ret;
		}
		$ret = call_user_func_array(array(&$model, $method), $args);
		$this->enabled = true;
		if($ret === false) {
			$_ret = "{false}";
		} else {
			$_ret = $ret;
		}	
		Cache::write($cachekey, $_ret, '_cake_data_');
		// クリア用にモデル毎のキャッシュキーリストを作成
		$cacheListKey = get_class($model) . '_cacheMethodList';
		$list = Cache::read($cacheListKey);
		$list[$cachekey] = 1;
		Cache::write($cacheListKey, $list);
		return $ret;
		
	}
/**
 * 再帰防止判定用
 * 
 * @param Model $model
 * @return boolean
 * @access public
 */
	function cacheEnabled(&$model){
		
		return $this->enabled;
		
	}
/**
 * キャッシュを削除する
 * 
 * @param Model $model
 * @return void
 * @access public
 */
	function cacheDelete(&$model){
		
		$cacheListKey = get_class($model) . '_cacheMethodList';
		$list = Cache::read($cacheListKey);
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
	function afterSave(&$model, $created) {
		
		$this->deleteAssocCache($model);
		
	}
/**
 * afterDelete
 * 
 * @param Model $model 
 * @return void
 * @access public
 */
	function afterDelete(&$model) {
		
		$this->deleteAssocCache($model);
		
	}
/**
 * 関連モデルを含めてキャッシュを削除する
 * 
 * @param Model $model
 * @return void
 * @access public
 * @todo 現在、3階層まで再帰対応。CakePHPのrecursiveの仕組み合わせたい
 */
	function deleteAssocCache(&$model, $recursive = 0) {
		
		$this->cacheDelete($model);
		if($recursive <= 3) {
			$recursive++;
			$assocTypes = array('hasMany', 'hasOne', 'belongsTo', 'hasAndBelongsToMany');
			foreach($assocTypes as $assocType) {
				if($model->{$assocType}) {
					foreach($model->{$assocType} as $assoc) {
						$className = $assoc['className'];
						if(isset($model->{$className})) {
							$this->deleteAssocCache($model->{$className}, $recursive);
						}
					}
				}
			}
		}
		
	}
	
}
?>
