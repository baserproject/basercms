<?php

/**
 * ブログタグモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('BlogAppModel', 'Blog.Model');

/**
 * ブログタグモデル
 *
 * @package Blog.Model
 */
class BlogTag extends BlogAppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'BlogTag';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * HABTM
 *
 * @var array
 * @access public
 */
	public $hasAndBelongsToMany = array(
		'BlogPost' => array(
			'className' => 'Blog.BlogPost',
			'joinTable' => 'blog_posts_blog_tags',
			'foreignKey' => 'blog_tag_id',
			'associationForeignKey' => 'blog_post_id',
			'conditions' => '',
			'order' => '',
			'limit' => '',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => ''
	));

/**
 * validate
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'ブログタグを入力してください。'
			),
			'duplicate' => array(
				'rule' => array('duplicate', 'name'),
				'message' => '既に登録のあるタグです。'
			)
	));

}
