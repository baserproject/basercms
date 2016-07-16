<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Behavior
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * プラグインコンテンツビヘイビア
 *
 * 一つのプラグインに複数のコンテンツを持つ場合に、一つのコンテンツに対し
 * [http://example/コンテンツ名/コントローラー/アクション]形式のURLでアクセスする為のビヘイビア
 * プラグインコンテンツテーブルへの自動的なデータの追加と削除を実装する。
 *
 * @package Baser.Model.Behavior
 */
class BcPluginContentBehavior extends ModelBehavior {

/**
 * プラグインコンテンツモデル
 *
 * @var Model
 */
	public $PluginContent = null;

/**
 * setup
 *
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->PluginContent = ClassRegistry::init('PluginContent', 'Model');
	}

/**
 * beforeSave
 *
 * @param Model $model
 * @param Model $options
 * @return void
 */
	public function beforeSave(Model $model, $options = array()) {
		if (!$model->exists()) {
			$ret = $this->PluginContent->find('first', array('conditions' => array('PluginContent.name' => $model->data[$model->alias]['name'])));
			if ($ret) {
				// 新規登録で既に登録されている場合は、重複エラーとする
				$model->invalidate('name', '既に登録されています。');
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
 */
	public function afterSave(Model $model, $created, $options = array()) {
		// コンテンツIDを取得
		if ($created) {
			$contentId = $model->getLastInsertId();
		} else {
			$contentId = $model->data[$model->alias]['id'];
		}
		$pluginContent = $this->_generatePluginContentData($model, $contentId);

		/*		 * * データを保存 ** */
		if (isset($pluginContent['PluginContent']['id'])) {
			$this->PluginContent->set($pluginContent);
		} else {
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
 */
	protected function _generatePluginContentData(Model $model, $contentId = '') {
		// プラグイン名を取得
		$pluginName = $this->getPluginName($model->alias);

		/*		 * * プラグインコンテンツを取得 ** */
		$pluginContent = array();
		if ($contentId) {
			$conditions = array(
				'PluginContent.content_id' => $contentId,
				'PluginContent.plugin' => $pluginName
			);
			$pluginContent = $this->PluginContent->find('first', array('conditions' => $conditions));
			if (!$pluginContent) {
				$pluginContent = array();
			}
		}

		/*		 * * データを更新 ** */
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
 */
	public function beforeDelete(Model $model, $cascade = true) {
		// プラグインコンテンツを自動削除する
		$this->PluginContent->deleteAll(array('name' => $model->data[$model->alias]['name']));
	}

/**
 * プラグイン名を取得する
 * モデル名から文字列「Content」を除外した「プラグイン名」を取得
 *
 * @param string モデル名
 * @return string プラグイン名
 */
	public function getPluginName($modelName) {
		if (strpos($modelName, 'Content') === false) {
			return strtolower($modelName);
		} else {
			return strtolower(str_replace('Content', '', $modelName));
		}
	}

}
