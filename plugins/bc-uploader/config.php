<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

$viewFilesPath = str_replace(ROOT, '', WWW_ROOT) . 'files';
return [
    'type' => 'CorePlugin',
    'title' => __d('baser', 'アップローダー'),
    'description' => __d('baser', 'Webページやブログ記事で、画像等のファイルを貼り付ける事ができます。'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'adminLink' => ['admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index'],
    'installMessage' => __d('baser', '登録ボタンをクリックする前に、サーバー上の ' . $viewFilesPath . ' に書き込み権限を与えてください。')
];
