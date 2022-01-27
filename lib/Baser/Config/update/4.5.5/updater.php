<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 4.4.6
 * @license			https://basercms.net/license/index.html
 */

/**
 * 4.4.6 バージョン アップデートスクリプト
 */

/**
 * search_indices テーブル構造変更
 */
    if($this->loadSchema('4.5.5', '', 'users', 'alter')) {
        $this->setUpdateLog('users テーブルの構造変更に成功しました。');
    } else {
        $this->setUpdateLog('users テーブルの構造変更に失敗しました。', true);
    }
