<?php
/* SVN FILE: $Id$ */
/**
 * ブログコンテンツモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.models
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
 * ブログコンテンツモデル
 *
 * @package			baser.plugins.blog.models
 */
class BlogContent extends BlogAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'BlogContent';
/**
 * behaviors
 *
 * @var 	array
 * @access 	public
 */
	var $actsAs = array('PluginContent');
/**
 * hasMany
 *
 * @var		array
 * @access 	public
 */
	var $hasMany = array('BlogPost'=>
			array('className'=>'Blog.BlogPost',
							'order'=>'id DESC',
							'limit'=>10,
							'foreignKey'=>'blog_content_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''),
			'BlogCategory'=>
			array('className'=>'Blog.BlogCategory',
							'order'=>'id',
							'limit'=>10,
							'foreignKey'=>'blog_content_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * beforeValidate
 *
 * @return	void
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => 'halfText',
						'message' => '>> ブログアカウント名は半角のみ入力して下さい',
						'allowEmpty'=>false),
				array(	'rule' => array('isUnique'),
						'on' => 'create',
						'message' => '入力されたブログアカウント名は既に使用されています。'));
		$this->validate['title'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> ブログタイトルを入力して下さい"));
		$this->validate['layout'] = array(array('rule' => 'halfText',
						'message' => '>> レイアウトテンプレート名は半角で入力して下さい',
						'allowEmpty'=>false));
		$this->validate['template'] = array(array(	'rule' => 'halfText',
						'message' => ">> コンテンツテンプレート名は半角で入力して下さい",
						'allowEmpty'=>false));
		$this->validate['list_count'] = array(array(	'rule' => 'halfText',
						'message' => ">> 一覧表示件数は半角で入力して下さい。",
						'allowEmpty'=>false));
		$this->validate['list_direction'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> 一覧に表示する順番を指定して下さい。"));
		return true;
	}
/**
 * 英数チェック
 *
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
 */
	function alphaNumeric($check) {

		if(preg_match("/^[a-z0-9]+$/",$check[key($check)])) {
			return true;
		}else {
			return false;
		}

	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null,$options = array()) {

		$controlSources['id'] = $this->find('list');

		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
}
?>