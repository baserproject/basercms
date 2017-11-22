<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * エディタテンプレート　モデル
 *
 * @package Baser.Model
 */
class EditorTemplate extends AppModel {

/**
 * モデル名
 * 
 * @var string 
 */
	public $name = 'EditorTemplate';

/**
 * behaviors
 *
 * @var 	array
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
 * バリデーション
 *
 * @var array
 */
	public $validate = [
		'name' => [
			['rule' => ['notBlank'],
				'message' => 'テンプレート名を入力してください。']
		]
	];
}
