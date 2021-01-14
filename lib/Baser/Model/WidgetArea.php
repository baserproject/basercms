<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class WidgetArea
 *
 * ウィジェットエリアモデル
 *
 * @package Baser.Model
 */
class WidgetArea extends AppModel
{

	/**
	 * クラス名
	 * @var string
	 */
	public $name = 'WidgetArea';

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * WidgetArea constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'name' => [
				'notBlank' => ['rule' => ['notBlank'], 'message' => __d('baser', 'ウィジェットエリア名を入力してください。')],
				'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'ウィジェットエリア名は255文字以内で入力してください。')]]
		];
	}

	/**
	 * コントロールソース取得
	 * @param string $field
	 * @return array
	 */
	public function getControlSource($field)
	{
		$controllSource['id'] = $this->find('list');
		if (isset($controllSource[$field])) {
			return $controllSource[$field];
		} else {
			return [];
		}
	}

}
