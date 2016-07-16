<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * プラグイン拡張クラス
 * プラグインのモデルより継承して利用します。
 *
 * @package Baser.Model
 */
class BcPluginAppModel extends AppModel {

/**
 * The name of the DataSource connection that this Model uses
 *
 * @var		string
 */
	public $useDbConfig = 'plugin';

}
