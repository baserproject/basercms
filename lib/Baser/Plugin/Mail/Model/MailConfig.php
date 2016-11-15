<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メール設定モデル
 *
 * @package Mail.Model
 *
 */
class MailConfig extends MailAppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'MailConfig';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = array('BcCache');

}
