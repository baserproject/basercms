<?php
/* SVN FILE: $Id$ */
/**
 * フィード設定モデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.feed.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * フィード設定モデル
 *
 * @package			baser.plugins.feed.models
 */
class FeedConfig extends FeedAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'FeedConfig';var $useDbConfig = 'plugin';
/**
 * hasMany
 * 
 * @var 	array
 * @access 	public
 */
	var $hasMany = array("FeedDetail" =>
							array("className" => "Feed.FeedDetail",
									"conditions" => "",
									"order" => "FeedDetail.id ASC",
									"limit" => 10,
									"foreignKey" => "feed_config_id",
									"dependent" => true,
									"exclusive" => false,
									"finderQuery" => ""));
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){
		
		$this->validate['name'] = array(array('rule' => array('minLength',1),
											'message' => ">> フィード設定名を入力して下さい",
											'required' => true));
		$this->validate['display_number'] = array(array('rule' => 'numeric',
											'message' => ">> 数値を入力して下さい",
											'required' => true));
		return true;
	}
/**
 * 初期値を取得
 */
 	function getDefaultValue(){
	
		$data['FeedConfig']['jquery'] = 0;
		return $data;
	
	}
	
}
?>