<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.Config
 * @since           baserCMS v 4.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * 4.5.0 バージョン アップデートスクリプト
 */

/**
 * mail_contents テーブル構造変更
 */
if($this->loadSchema('4.5.0', 'Mail', 'mail_fields', $filterType = 'alter')) {
	$this->setUpdateLog('mail_fields テーブルの構造変更に成功しました。');
} else {
	$this->setUpdateLog('mail_fields テーブルの構造変更に失敗しました。', true);
}
