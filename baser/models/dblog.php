<?php
/* SVN FILE: $Id$ */
/**
 * DBログモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * DBログモデル
 *
 * @package			baser.models
 */
class Dblog extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Dblog';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Cache');
/**
 * belongsTo
 * @var 	array
 * @access	public
 */
	var $belongsTo = array(
			'User' => array(
				'className'=> 'User',
				'foreignKey'=>'user_id'
	));
/**
 * バリデーション
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'name'	=> array(
			array(	'rule' => array('notEmpty'),
					'message' => "ログ内容を入力してください。",
					'required' => true)
		)
	);

}
?>