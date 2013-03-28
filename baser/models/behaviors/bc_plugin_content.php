<?php
/* SVN FILE: $Id$ */
/**
 * プラグインコンテンツビヘイビア
 *
 * 一つのプラグインに複数のコンテンツを持つ場合に、一つのコンテンツに対し
 * [http://example/コンテンツ名/コントローラー/アクション]形式のURLでアクセスする為のビヘイビア
 * プラグインコンテンツテーブルへの自動的なデータの追加と削除を実装する。
 *
 * 以下が必須項目
 * ◆ /app/config/plugin.php
 * ◆ /app/models/plugin_content.php
 * ◆ plugin_contents テーブル
 * 詳しくは、/app/config/plugin.php を参照
 *
 * 【注意点】
 * このビヘイビアを実装するモデルはプラグイン名と同じモデルもしくは、[プラグイン名Content]である必要がある
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
 * プラグインコンテンツビヘイビア
 *
 * @package baser.models.behaviors
 */
class BcPluginContentBehavior extends ModelBehavior {
/**
 * プラグインコンテンツモデル
 *
 * @var Model
 * @access public
 */
	var $PluginContent = null;
/**
 * setup
 *
 * @param array $config
 * @return void
 * @access public
 */
	function setup($model, $config = array()) {

		$this->PluginContent = ClassRegistry::init('PluginContent', 'Model');

	}
/**
 * beforeSave
 *
 * @param Model $model
 * @param Model $options
 * @return void
 * @access public
 */
	function beforeSave($model,$options) {

		if(!$model->exists()) {
			$ret = $this->PluginContent->find(array('PluginContent.name'=>$model->data[$model->alias]['name']));
			if($ret) {
				// 新規登録で既に登録されている場合は、重複エラーとする
				$model->invalidate('name','既に登録されています。');
				return false;
			}
			$pluginContent = $this->_generatePluginContentData($model);
			$this->PluginContent->create($pluginContent);
		} else {
			$pluginContent = $this->_generatePluginContentData($model, $model->data[$model->alias]['id']);
			$this->PluginContent->set($pluginContent);
		}
		
		// バリデーション
		return $this->PluginContent->validates();

	}
/**
 * afterSave
 *
 * @param	Model	$model
 * @param Model $created
 * @return	void
 * @access	public
 */
	function afterSave($model, $created) {

		// コンテンツIDを取得
		if($created) {
			$contentId = $model->getLastInsertId();
		}else {
			$contentId = $model->data[$model->alias]['id'];
		}
		$pluginContent = $this->_generatePluginContentData($model, $contentId);
		
		/*** データを保存 ***/
		if(isset($pluginContent['PluginContent']['id'])) {
			$this->PluginContent->set($pluginContent);
		}else {
			$this->PluginContent->create($pluginContent);
		}
		$this->PluginContent->save();

	}
/**
 * プラグインコンテンツデータを生成する
 * 既に登録されているデータの場合は取得した上で生成
 *
 * @param Model $model
 * @param Model $contentId
 * @return	array
 * @access	protected
 */
	function _generatePluginContentData($model, $contentId = '') {
		
		// プラグイン名を取得
		$pluginName = $this->getPluginName($model->alias);
		
		/*** プラグインコンテンツを取得 ***/
		$pluginContent = array();
		if($contentId) {
			$conditions = array('PluginContent.content_id'=>$contentId,
					'PluginContent.plugin'=>$pluginName);
			$pluginContent = $this->PluginContent->find($conditions);
			if(!$pluginContent) {
				$pluginContent = array();
			}
		}

		/*** データを更新 ***/
		$pluginContent['PluginContent']['plugin'] = $pluginName;
		$pluginContent['PluginContent']['content_id'] = $contentId;
		$pluginContent['PluginContent']['name'] = $model->data[$model->alias]['name'];

		return $pluginContent;
		
	}
/**
 * beforeDelete
 *
 * @param	Model	$model
 * @return	void
 * @access	public
 */
	function beforeDelete($model) {

		// プラグインコンテンツを自動削除する
		$this->PluginContent->deleteAll(array('name'=>$model->data[$model->alias]['name']));

	}
/**
 * プラグイン名を取得する
 * モデル名から文字列「Content」を除外した「プラグイン名」を取得
 *
 * @param string モデル名
 * @return string プラグイン名
 */
	function getPluginName($modelName) {

		if(strpos($modelName,'Content')===false) {
			return strtolower($modelName);
		}else {
			return strtolower(str_replace('Content','',$modelName));
		}

	}

}
