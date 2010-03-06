<?php
/* SVN FILE: $Id$ */
/**
 * ブログコメントモデル
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
 * ブログコメントモデル
 *
 * @package			baser.plugins.blog.models
 */
class BlogComment extends BlogAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'BlogComment';
/**
 * belongsTo
 *
 * @var 	array
 * @access	public
 */
 	var $belongsTo = array('BlogPost' =>    array(  'className'=>'Blog.BlogPost',
                                                        'foreignKey'=>'blog_post_id'));
/**
 * beforeValidate
 *
 * @return	void
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => '>> お名前は必ず入力して下さい'));
		$this->validate['message'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> コメントを入力して下さい"));
		return true;
	}
/**
 * 初期値を取得する
 *
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue(){
		$data[$this->name]['name'] = 'NO NAME';
		return $data;
	}
}
?>