<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.3.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.3.0 アップデーター
 *
 * CakePHP 5.1 より Cache設定 `_cake_core_` が非推奨となり
 * `_cake_translations_` を利用することになったため、
 * config/app.php と config/bootstrap.php における設定名を書き換える。
 */
use BaserCore\Utility\BcUpdateLog;

$targets = [
    ROOT . DS . 'config' . DS . 'app.php',
    ROOT . DS . 'config' . DS . 'bootstrap.php',
];

foreach($targets as $target) {

    if (!file_exists($target)) {
        BcUpdateLog::set(__d('baser_core', '{0} が存在しないため、Cache設定名の書き換えをスキップしました。', $target));
        continue;
    }

    $content = file_get_contents($target);
    if ($content === false) {
        BcUpdateLog::set(__d('baser_core', '{0} の読み込みに失敗しました。手動で `_cake_core_` を `_cake_translations_` に書き換えてください。', $target));
        continue;
    }

    // 書き換え対象がない場合は何もしない
    if (!str_contains($content, '_cake_core_')) continue;

    // 「myapp_cake_core_」のようなプレフィックスも合わせて書き換える
    $converted = str_replace('_cake_core_', '_cake_translations_', $content);

    if (!is_writable($target)) {
        BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がないため、書き換えできませんでした。書き込み権限を与えた上で、手動で `_cake_core_` を `_cake_translations_` に書き換えてください。', $target));
        continue;
    }

    if (file_put_contents($target, $converted) === false) {
        BcUpdateLog::set(__d('baser_core', '{0} の書き込みに失敗しました。手動で `_cake_core_` を `_cake_translations_` に書き換えてください。', $target));
        continue;
    }

    BcUpdateLog::set(__d('baser_core', '{0} における Cache設定名 `_cake_core_` を `_cake_translations_` に書き換えました。', $target));
}
