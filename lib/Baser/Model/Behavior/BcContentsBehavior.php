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
 * Class BcContentsBehavior
 *
 * baserCMS Contents Behavior
 *
 * @package Baser.Model.Behavior
 */
class BcContentsBehavior extends ModelBehavior
{

	/**
	 * 削除したデータに関連する Content ID を一時保管する為に利用
	 *
	 * @var int
	 */
	protected $_deleteContentId = null;

	/**
	 * Setup
	 *
	 * @param Model $model
	 * @param array $config
	 * @return mixed
	 */
	public function setup(Model $model, $config = [])
	{
		$model->hasOne['Content'] = [
			'className' => 'Content',
			'foreignKey' => 'entity_id',
			'dependent' => false,
			'conditions' => [
				'Content.type' => $model->name,
				'Content.alias_id' => null
			]
		];
		return true;
	}

	/**
	 * Before validate
	 *
	 * Content のバリデーションを実行
	 * 本体のバリデーションも同時に実行する為、Contentのバリデーション判定は、 beforeSave にて確認
	 *
	 * @param Model $model
	 * @param array $options
	 * @return bool
	 */
	public function beforeValidate(Model $model, $options = [])
	{
		if (!empty($model->data['Content'])) {
			$model->Content->clear();
			$model->Content->set($model->data['Content']);
			$model->Content->validates($options);
			if ($model->Content->validationErrors && empty($model->data['Content']['id'])) {
				$model->validationErrors += $model->Content->validationErrors;
			}
			if (!empty($model->Content->data['Content'])) {
				$model->data['Content'] = $model->Content->data['Content'];
			}
		}
		return true;
	}

	/**
	 * Before save
	 *
	 * Content のバリデーション結果確認
	 *
	 * @param Model $model
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave(Model $model, $options = [])
	{
		if (!empty($options['validate'])) {
			if ($model->Content->validationErrors) {
				return false;
			}
		}
		return true;
	}

	/**
	 * After save
	 *
	 * Content を保存する
	 *
	 * @param Model $model
	 * @param bool $created
	 * @param array $options
	 * @return bool
	 */
	public function afterSave(Model $model, $created, $options = [])
	{
		if (empty($model->Content->data['Content'])) {
			return;
		}
		if (!empty($options['validate'])) {
			// beforeValidate で調整したデータを利用する為、$model->Content->data['Content'] を利用
			$data = $model->Content->data['Content'];
		} else {
			$data = $model->data['Content'];
		}
		unset($data['lft']);
		unset($data['rght']);
		if ($created) {
			$data = $model->Content->createContent($data, ($model->plugin)? $model->plugin : "Core", $model->name, $model->id, false);
		} else {
			$model->Content->clear();
			$data = $model->Content->save($data, false);
		}
		if (!empty($data['Content'])) {
			$model->data['Content'] = $data['Content'];
		}
	}

	/**
	 * Before delete
	 *
	 * 削除した Content ID を一旦保管し、afterDelete で Content より削除する
	 *
	 * @param Model $model
	 * @param bool $cascade
	 * @return bool
	 */
	public function beforeDelete(Model $model, $cascade = true)
	{
		$data = $model->find('first', [
			'conditions' => [$model->alias . '.id' => $model->id]
		]);
		if (!empty($data['Content']['id'])) {
			$this->_deleteContentId = $data['Content']['id'];
		}
		return true;
	}

	/**
	 * After delete
	 *
	 * 削除したデータに連携する Content を削除
	 *
	 * @param Model $model
	 */
	public function afterDelete(Model $model)
	{
		if ($this->_deleteContentId) {
			$softDelete = $model->Content->softDelete(null);
			$model->Content->softDelete(false);
			$model->Content->removeFromTree($this->_deleteContentId, true);
			$model->Content->softDelete($softDelete);
			$this->_deleteContentId = null;
		}
	}

	/**
	 * 公開されたコンテンツを取得する
	 *
	 * @param Model $model
	 * @param string $type
	 * @param array $query
	 * @return array|null
	 */
	public function findPublished(Model $model, $type = 'first', $query = [])
	{
		$conditionAllowPublish = $model->Content->getConditionAllowPublish();
		if (!empty($query['conditions'])) {
			$query['conditions'] = array_merge(
				$conditionAllowPublish,
				$query['conditions']
			);
		} else {
			$query['conditions'] = $conditionAllowPublish;
		}
		return $model->find($type, $query);
	}

}
