<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.9
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.2.9 アップデーター
 *
 * baserCMS 5.3系より config/plugins.php にて null の指定が不可となったため、
 * null が指定されている場合、空配列に書き換える。
 */
use BaserCore\Utility\BcUpdateLog;

$pluginsPhp = ROOT . DS . 'config' . DS . 'plugins.php';

if (!file_exists($pluginsPhp)) {
    BcUpdateLog::set(__d('baser_core', '{0} が存在しないため、null から配列への変換をスキップしました。', $pluginsPhp));
    return;
}

$content = file_get_contents($pluginsPhp);
if ($content === false) {
    BcUpdateLog::set(__d('baser_core', '{0} の読み込みに失敗しました。手動で null の指定を [] に書き換えてください。', $pluginsPhp));
    return;
}

// 「=> null」を「=> []」に置換する
$converted = preg_replace('/(=>\s*)null(\s*[,)\]])/i', '$1[]$2', $content);

if ($converted === null) {
    BcUpdateLog::set(__d('baser_core', '{0} の変換に失敗しました。手動で null の指定を [] に書き換えてください。', $pluginsPhp));
    return;
}

if ($converted === $content) {
    // 変換対象がない場合は何もしない
    return;
}

if (!is_writable($pluginsPhp)) {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がないため、変換できませんでした。書き込み権限を与えた上で、手動で null の指定を [] に書き換えてください。', $pluginsPhp));
    return;
}

if (file_put_contents($pluginsPhp, $converted) === false) {
    BcUpdateLog::set(__d('baser_core', '{0} の書き込みに失敗しました。手動で null の指定を [] に書き換えてください。', $pluginsPhp));
    return;
}

BcUpdateLog::set(__d('baser_core', '{0} における null の指定を配列に変換しました。', $pluginsPhp));
