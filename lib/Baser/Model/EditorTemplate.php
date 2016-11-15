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
	public $actsAs = array(
		'BcUpload' => array(
			'saveDir' => "editor",
			'fields' => array(
				'image' => array(
					'type' => 'image',
					'namefield' => 'id',
					'nameadd' => false,
					'imageresize' => array('prefix' => 'template', 'width' => '100', 'height' => '100')
				)
			)
		)
	);

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notBlank'),
				'message' => 'テンプレート名を入力してください。')
		)
	);
	
}
