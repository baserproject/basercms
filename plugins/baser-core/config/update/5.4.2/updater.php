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
 * 1. キャッシュクリアを POST のみ受け付けるように変更したため（CSRF 対策）、
 *    既存のアクセスルールで method が GET になっているキャッシュクリアのルールを POST に書き換える。
 *    GET のままだと、POST のリクエストが拒否され、管理者以外のユーザーがキャッシュクリアを利用できなくなる。
 *    5.4.0 / 5.4.1 から更新する場合は 5.3.2 アップデーターが実行されないため、同じ処理を行う（再実行しても影響はない）。
 *
 * 2. config/install.php の各データソース設定に `cacheMetadata => true` を追記する。
 *    この設定が無いと CakePHP はテーブル構造（カラム・インデックス等）をキャッシュせず、
 *    利用するテーブルごとに毎リクエスト DESCRIBE 相当のクエリが発生する（1 画面で 50 本前後）。
 *    5.4.2 以降のインストーラは生成時に出力するが、アップデートで導入済みのサイトは
 *    install.php が書き換わらないため、ここで追記する。
 *    テストから対象ファイルを差し替えられるよう、$installFile が定義済みの場合はそれを使う。
 *
 * 2. は途中で終了する（return）ことがあるため、1. を先に実行する。
 */
use BaserCore\Utility\BcUpdateLog;
use Cake\ORM\TableRegistry;

// 1. キャッシュクリアのアクセスルールを POST に書き換える
$clearCacheUrls = [
    '/baser/admin/baser-core/utilities/clear_cache',
    '/baser/api/admin/baser-core/utilities/clear_cache.json',
];

try {
    $permissionCount = TableRegistry::getTableLocator()->get('BaserCore.Permissions')->updateAll(
        ['method' => 'POST'],
        ['url IN' => $clearCacheUrls, 'method' => 'GET']
    );
    if ($permissionCount) {
        BcUpdateLog::set(__d('baser_core', 'キャッシュクリアのアクセスルール {0} 件のメソッドを POST に変更しました。', $permissionCount));
    }
} catch (\Throwable $e) {
    BcUpdateLog::set(__d('baser_core', 'キャッシュクリアのアクセスルールの書き換えに失敗しました。アクセスルール管理にて、キャッシュクリアのルールのメソッドを POST に変更してください。') . ' ' . $e->getMessage());
}

// 2. config/install.php に cacheMetadata を追記する
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
