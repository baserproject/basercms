<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.11
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.2.11 アップデーター
 *
 * キャッシュクリアを POST のみ受け付けるように変更したため（CSRF 対策）、
 * 既存のアクセスルールで method が GET になっているキャッシュクリアのルールを POST に書き換える。
 * GET のままだと、POST のリクエストが拒否され、管理者以外のユーザーがキャッシュクリアを利用できなくなる。
 */
use BaserCore\Utility\BcUpdateLog;
use Cake\ORM\TableRegistry;

$urls = [
    '/baser/admin/baser-core/utilities/clear_cache',
    '/baser/api/admin/baser-core/utilities/clear_cache.json',
];

try {
    $count = TableRegistry::getTableLocator()->get('BaserCore.Permissions')->updateAll(
        ['method' => 'POST'],
        ['url IN' => $urls, 'method' => 'GET']
    );
} catch (\Throwable $e) {
    BcUpdateLog::set(__d('baser_core', 'キャッシュクリアのアクセスルールの書き換えに失敗しました。アクセスルール管理にて、キャッシュクリアのルールのメソッドを POST に変更してください。') . ' ' . $e->getMessage());
    return;
}

if ($count) {
    BcUpdateLog::set(__d('baser_core', 'キャッシュクリアのアクセスルール {0} 件のメソッドを POST に変更しました。', $count));
}
