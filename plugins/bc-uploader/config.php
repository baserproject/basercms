<?php
$viewFilesPath = str_replace(ROOT, '', WWW_ROOT) . 'files';
return [
    'title' => __d('baser', 'アップローダー'),
    'description' => __d('baser', 'Webページやブログ記事で、画像等のファイルを貼り付ける事ができます。'),
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'adminLink' => ['admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index'],
    'installMessage' => __d('baser', '登録ボタンをクリックする前に、サーバー上の ' . $viewFilesPath . ' に書き込み権限を与えてください。')
];
