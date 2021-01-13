<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Model
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * ファイルカテゴリモデル
 *
 * @package         Uploader.Model
 */
class UploaderCategory extends AppModel
{

	/**
	 * プラグイン名
	 *
	 * @var        string
	 * @access    public
	 */
	public $plugin = 'Uploader';
	/**
	 * バリデート
	 *
	 * @var        array
	 * @access    public
	 */
	public $validate = [
		'name' => [
			[
				'rule' => ['notBlank'],
				'message' => 'カテゴリ名を入力してください。']
		]
	];

	/**
	 * コピーする
	 *
	 * @param int $id
	 * @param array $data
	 * @return mixed page Or false
	 */
	public function copy($id = null, $data = [])
	{

		if ($id) {
			$data = $this->find('first', ['conditions' => ['UploaderCategory.id' => $id]]);
		}
		$oldData = $data;

		// EVENT UploaderCategory.beforeCopy
		$event = $this->dispatchEvent('beforeCopy', [
			'data' => $data,
			'id' => $id,
		]);
		if ($event !== false) {
			$data = $event->result === true? $event->data['data'] : $event->result;
		}

		$data['UploaderCategory']['name'] .= '_copy';
		$data['UploaderCategory']['id'] = $this->getMax('id', ['UploaderCategory.id' => $data['UploaderCategory']['id']]) + 1;

		unset($data['UploaderCategory']['id']);
		unset($data['UploaderCategory']['created']);
		unset($data['UploaderCategory']['modified']);

		$this->create($data);
		$result = $this->save();
		if ($result) {
			$result['UploaderCategory']['id'] = $this->getLastInsertID();
			$data = $result;

			// EVENT UploaderCategory.afterCopy
			$event = $this->dispatchEvent('afterCopy', [
				'id' => $data['UploaderCategory']['id'],
				'data' => $data,
				'oldId' => $id,
				'oldData' => $oldData,
			]);

			return $result;
		} else {
			if (isset($this->validationErrors['name'])) {
				return $this->copy(null, $data);
			} else {
				return false;
			}
		}

	}

}
