<?php
/* SVN FILE: $Id$ */
/**
 * ブログタグモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.models
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
 * ブログタグモデル
 *
 * @package baser.plugins.blog.models
 */
class BlogTag extends BlogAppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogTag';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('BcCache');
/**
 * HABTM
 *
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array(
			'BlogPost' => array(
				'className'				=> 'Blog.BlogPost',
				'joinTable'				=> 'blog_posts_blog_tags',
				'foreignKey'			=> 'blog_tag_id',
				'associationForeignKey'	=> 'blog_post_id',
				'conditions'			=> '',
				'order'					=> '',
				'limit'					=> '',
				'unique'				=> true,
				'finderQuery'			=> '',
				'deleteQuery'			=> ''
		));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name'=>array(
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> 'ブログタグを入力してください。'
			),
			'duplicate' => array(
				'rule'		=>	array('duplicate','name'),
				'message'	=> '既に登録のあるタグです。'
			)
		));
}
