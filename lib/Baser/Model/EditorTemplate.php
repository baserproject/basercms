<?php
/* SVN FILE: $Id$ */
/**
 * エディタテンプレート　モデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * エディタテンプレート　モデル
 *
 * @package baser.models
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
			'saveDir'	=> "editor",
			'fields'	=> array(
				'image'	=> array(
					'type'			=> 'image',
					'namefield'		=> 'id',
					'nameadd'		=> false,
					'imageresize'	=> array('prefix' => 'template', 'width' => '100', 'height' => '100')
				)
			)
		)
	);
}