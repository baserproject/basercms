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
    'type' => 'CorePlugin',
    'title' => __d('baser_core', 'アップローダー'),
    'description' => __d('baser_core', 'Webページやブログ記事で、画像等のファイルを貼り付ける事ができます。'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'adminLink' => ['prefix' => 'Admin', 'plugin' => 'BcUploader', 'controller' => 'UploaderFiles', 'action' => 'index'],
    'installMessage' => __d('baser_core', '登録ボタンをクリックする前に、サーバー上の ' . $viewFilesPath . ' に書き込み権限を与えてください。')
];
