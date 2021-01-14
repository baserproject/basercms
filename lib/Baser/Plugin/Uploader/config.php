<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

$title = __d('baser', 'アップローダー');
$description = __d('baser', 'Webページやブログ記事で、画像等のファイルを貼り付ける事ができます。');
$author = 'baserCMS Users Community';
$url = 'https://basercms.net';
$adminLink = ['admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index'];
if (!is_writable(WWW_ROOT . 'files')) {
	$viewFilesPath = str_replace(ROOT, '', WWW_ROOT) . 'files';
	$installMessage = __d('baser', '登録ボタンをクリックする前に、サーバー上の ' . $viewFilesPath . ' に書き込み権限を与えてください。');
}
