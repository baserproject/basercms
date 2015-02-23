<?php

/**
 * プラグイン拡張クラス
 * 
 * PHP versions 5
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
 * Include files
 */

/**
 * プラグイン拡張クラス
 * プラグインのモデルより継承して利用します。
 * @package Baser.Model
 */
class BcPluginAppModel extends AppModel {

/**
 * The name of the DataSource connection that this Model uses
 *
 * @var		string
 * @access 	public
 */
	public $useDbConfig = 'plugin';

}
