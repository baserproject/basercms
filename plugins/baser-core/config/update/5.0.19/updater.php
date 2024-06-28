<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.19
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.0.19 アップデーター
 *
 * baserCMS5.1系対応の準備処理を実行
 */
use BaserCore\Utility\BcUpdateLog;

$updateDir = __DIR__;

if(!file_exists(ROOT . DS . 'config' . DS . 'plugins.php')) {
    if(!copy($updateDir . DS . 'config' . DS . 'plugins.php', ROOT . DS . 'config' . DS . 'plugins.php')) {
        BcUpdateLog::set(__d('baser_core', 'plugins.php に配置に失敗しました。' . $updateDir . DS . 'config' . DS . 'plugins.php をコピーして、手動で' . ROOT . DS . 'config 配下に配置してください。'));
    }
}
