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

$viewFilesPath = str_replace(ROOT, '', WWW_ROOT) . 'files';
return [
    'type' => 'Plugin',
    'title' => __d('baser_core', 'サンプルプラグイン'),
    'description' => __d('baser_core', 'ツールバーに少しの変化をもたらすだけのサンプルプラグインです。'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
];
