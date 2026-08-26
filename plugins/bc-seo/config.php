<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

return [
    'type' => 'CorePlugin',
    'title' => __d('baser_core', 'SEO設定'),
    'description' => __d('baser_core', 'コンテンツやブログ記事などにSEO用の設定を追加します。'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'installMessage' => __d('baser_core', 'インストールしただけでは、SEO設定はフロントページに出力されません。利用中のテーマのレイアウトへのタグの追加が必要です。詳しくは <a href="https://baserproject.github.io/5/operation/application/seo" target="_blank" class="outside-link">SEO設定のマニュアル</a> を確認してください。'),
    'adminLink' => ['plugin' => 'BcSeo', 'controller' => 'SeoConfigs', 'action' => 'update_db'],
];
