<?php

/* SVN FILE: $Id$ */
/**
 * ブログ設定モデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Plugin.Blog.Model
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('BlogAppModel', 'Blog.Model');

/**
 * ブログ設定モデル
 *
 * @package Baser.Plugin.Blog.Model
 */
class BlogConfig extends BlogAppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'BlogConfig';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

}
