<?php
/* SVN FILE: $Id$ */
/**
 * フィード詳細モデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.models
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
 * feed_detail
 *
 * @package baser.plugins.feed.models
 */
class FeedDetail extends FeedAppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'FeedDetail';
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
	var $belongsTo = array('FeedConfig'=>array('className'=>'Feed.FeedConfig',
							'conditions' => '',
							'order'=> '',
							'foreignKey' => 'feed_config_id'
	));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "フィード詳細名を入力してください。",
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'フィード詳細名は50文字以内で入力してください。')
		),
		'url' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "フィードURLを入力してください。",
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'フィードURLは255文字以内で入力してください。')
		),
		'category_filter' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'カテゴリフィルタは255文字以内で入力してください。')
		)
	);
/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @return array コントロールソース
 * @access	public
 */
	function getControlSource($field = null) {

		$controlSources['cache_time'] = array('+1 minute'=>'1分',
				'+30 minutes'=>'30分',
				'+1 hour'=>'1時間',
				'+6 hours'=>'6時間',
				'+24 hours'=>'1日');
		return $controlSources[$field];

	}
/**
 * 初期値を取得する
 * 
 * @param string $feedDetailId
 * @retun array $data
 * @access public
 */
	function getDefaultValue($feedConfigId) {

		$feedConfig = $this->FeedConfig->find(array('FeedConfig.id'=>$feedConfigId));
		$data[$this->name]['feed_config_id'] = $feedConfigId;
		$data[$this->name]['name'] = $feedConfig['FeedConfig']['name'];
		$data[$this->name]['cache_time'] = '+30 minutes';
		return $data;

	}
	
}
