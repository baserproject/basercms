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
 * Class EditorTemplate
 *
 * エディタテンプレート　モデル
 *
 * @package Baser.Model
 */
class EditorTemplate extends AppModel
{

	/**
	 * モデル名
	 *
	 * @var string
	 */
	public $name = 'EditorTemplate';

	/**
	 * behaviors
	 *
	 * @var    array
	 */
	public $actsAs = [
		'BcUpload' => [
			'saveDir' => "editor",
			'fields' => [
				'image' => [
					'type' => 'image',
					'namefield' => 'id',
					'nameadd' => false,
					'imageresize' => ['prefix' => 'template', 'width' => '100', 'height' => '100']
				]
			]
		]
	];

	/**
	 * EditorTemplate constructor.
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
				['rule' => ['notBlank'], 'message' => __d('baser', 'テンプレート名を入力してください。')]],
			'image' => [
				['rule' => ['fileExt', ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png']], 'message' => __d('baser', '許可されていないファイルです。')]]
		];
	}

}
