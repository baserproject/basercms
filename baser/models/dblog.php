<?php
/* SVN FILE: $Id$ */
/**
 * DBログモデル
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
 * DBログモデル
 *
 * @package baser.models
 */
class Dblog extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Dblog';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('BcCache');
/**
 * belongsTo
 * 
 * @var array
 * @access public
 */
	var $belongsTo = array(
			'User' => array(
				'className'=> 'User',
				'foreignKey'=>'user_id'
	));
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name'	=> array(
			array(	'rule' => array('notEmpty'),
					'message' => "ログ内容を入力してください。",
					'required' => true)
		)
	);

}
