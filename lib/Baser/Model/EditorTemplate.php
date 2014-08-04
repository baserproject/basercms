<?php

/**
 * エディタテンプレート　モデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
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
 * @access 	public
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
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => 'テンプレート名を入力してください。')
		)
	);
	
}
