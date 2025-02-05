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
    'type' => ['BcCustomContentPlugin', 'Plugin'],
    'title' => __d('baser_core', 'カスタムコンテンツ&BugerEditor連携'),
    'description' => __d('baser_core', 'カスタムコンテンツでBugerEditorを使用するためのプラグインです。'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'adminLink' => ['plugin' => 'BcCustomContent', 'controller' => 'CustomTables', 'action' => 'index'],
    'installMessage' => ''
];
