<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.2
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.4.2 アップデーター
 *
 * config/install.php の各データソース設定に `cacheMetadata => true` を追記する。
 *
 * この設定が無いと CakePHP はテーブル構造（カラム・インデックス等）をキャッシュせず、
 * 利用するテーブルごとに毎リクエスト DESCRIBE 相当のクエリが発生する（1 画面で 50 本前後）。
 * 5.4.2 以降のインストーラは生成時に出力するが、アップデートで導入済みのサイトは
 * install.php が書き換わらないため、ここで追記する。
 *
 * テストから対象ファイルを差し替えられるよう、$installFile が定義済みの場合はそれを使う。
 */
use BaserCore\Utility\BcUpdateLog;

$installFile = $installFile ?? ROOT . DS . 'config' . DS . 'install.php';

if (!file_exists($installFile)) {
    BcUpdateLog::set(__d('baser_core', '{0} が存在しないため、cacheMetadata の追記をスキップしました。', $installFile));
    return;
}

$content = file_get_contents($installFile);
if ($content === false) {
    BcUpdateLog::set(__d('baser_core', '{0} の読み込みに失敗しました。各データソース設定に `\'cacheMetadata\' => true` を手動で追記してください。', $installFile));
    return;
}

// 既に設定がある場合は、その値を尊重して何もしない
if (str_contains($content, 'cacheMetadata')) return;

// 各 'Datasources.xxx' => [ の直後に追記する（末尾カンマや 'log' 行の有無に依存しない）
$converted = preg_replace(
    '/(\'Datasources\.[A-Za-z0-9_]+\'\s*=>\s*\[)/',
    "$1\n        'cacheMetadata' => true,",
    $content,
    -1,
    $count
);

if ($converted === null || !$count) {
    BcUpdateLog::set(__d('baser_core', '{0} にデータソース設定が見つからなかったため、cacheMetadata の追記をスキップしました。各データソース設定に `\'cacheMetadata\' => true` を手動で追記してください。', $installFile));
    return;
}

if (!is_writable($installFile)) {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がないため、cacheMetadata を追記できませんでした。各データソース設定に `\'cacheMetadata\' => true` を手動で追記してください。', $installFile));
    return;
}

if (file_put_contents($installFile, $converted) === false) {
    BcUpdateLog::set(__d('baser_core', '{0} の書き込みに失敗しました。各データソース設定に `\'cacheMetadata\' => true` を手動で追記してください。', $installFile));
    return;
}

BcUpdateLog::set(__d('baser_core', '{0} の {1} 件のデータソース設定に `\'cacheMetadata\' => true` を追記しました。', $installFile, $count));
