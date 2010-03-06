<?php
/* SVN FILE: $Id$ */
/**
 * フィード詳細モデル
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
 * フィード詳細モデル
 *
 * @package			baser.plugins.feed.models
 */
class FeedDetail extends FeedAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'FeedDetail';
    var $belongsTo = array('FeedConfig'=>array('className'=>'Feed.FeedConfig',
                                            'conditions' => '',
                                            'order'=> '',
                                            'foreignKey' => 'feed_config_id'
                                            ));
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){
		
		$this->validate['name'] = array(array('rule' => array('minLength',1),
											'message' => ">> フィード詳細名を入力して下さい",
											'required' => true));
		return true;
	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null){

        $controlSources['cache_time'] = array('+1 minute'=>'1分',
                                              '+30 minutes'=>'30分',
                                              '+1 hour'=>'1時間',
                                              '+6 hours'=>'6時間',
                                              '+24 hours'=>'1日');
		return $controlSources[$field];

	}
/**
 * 初期値を取得する
 * @param string $feedDetailId
 * @retun array $data
 * @access public
 */
    function getDefaultValue($feedConfigId){

        $feedConfig = $this->FeedConfig->find(array('FeedConfig.id'=>$feedConfigId));
        $data[$this->name]['feed_config_id'] = $feedConfigId;
        $data[$this->name]['name'] = $feedConfig['FeedConfig']['name'];
        $data[$this->name]['cache_time'] = '+30 minutes';
        return $data;
        
    }
}
?>