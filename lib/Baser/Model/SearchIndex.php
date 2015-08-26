<?php

/**
 * 検索インデックスモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * 検索インデックスモデル
 *
 * @package Baser.Model
 */
class SearchIndex extends AppModel {

/**
 * クラス名
 * 
 * @var string
 * @access public
 */
	public $name = 'SearchIndex';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

}
