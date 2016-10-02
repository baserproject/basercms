<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BlogAppModel', 'Blog.Model');

/**
 * ブログ設定モデル
 *
 * @package Blog.Model
 */
class BlogConfig extends BlogAppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'BlogConfig';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = ['BcCache'];

}
