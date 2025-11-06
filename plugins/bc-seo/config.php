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
    'description' => 'コンテンツやブログ記事などにSEO用の設定を追加します。',
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'adminLink' => ['plugin' => 'BcSeo', 'controller' => 'SeoConfigs', 'action' => 'update_db'],
];
