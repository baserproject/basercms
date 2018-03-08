<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 4.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * 4.1.0 バージョン アップデートスクリプト
 */

/**
 * mail_contents テーブル構造変更
 */
    if($this->loadSchema('4.1.0', 'Mail', 'mail_contents', $filterType = 'alter')) {
        $this->setUpdateLog('メールプラグイン mail_contents テーブルの構造変更に成功しました。');
    } else {
        $this->setUpdateLog('メールプラグイン mail_contents テーブルの構造変更に失敗しました。', true);
    }
