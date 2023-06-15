<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

return [
    'type' => 'CorePlugin',
    'title' => __d('baser_core', 'カスタムコンテンツ'),
    'description' => __d('baser_core', '複数設置可能で独自フィールドが定義できるコンテンツ'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'adminLink' => ['plugin' => 'BcCustomContent', 'controller' => 'CustomTables', 'action' => 'index'],
    'installMessage' => ''
];
