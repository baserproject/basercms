<?php
/* SVN FILE: $Id$ */
/**
 * よく使う項目　モデル
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
 * よく使う項目　モデル
 *
 * @package baser.models
 */
class Favorite extends AppModel {
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Favorite';
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
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('BcCache');
}