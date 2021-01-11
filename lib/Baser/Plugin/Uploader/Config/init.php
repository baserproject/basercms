<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Config
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * データベース初期化
 */
$this->Plugin->initDb('Uploader');

/**
 * 必要フォルダ初期化
 */
$filesPath = WWW_ROOT . 'files';
$savePath = $filesPath . DS . 'uploads';
$limitedPath = $savePath . DS . 'limited';

if (is_writable($filesPath) && !is_dir($savePath)) {
	mkdir($savePath);
}
if (!is_writable($savePath)) {
	chmod($savePath, 0777);
}
if (is_writable($savePath) && !is_dir($limitedPath)) {
	mkdir($limitedPath);
}
if (!is_writable($limitedPath)) {
	chmod($limitedPath, 0777);
}
if (is_writable($limitedPath)) {
	$File = new File($limitedPath . DS . '.htaccess');
	$htaccess = "Order allow,deny\nDeny from all";
	$File->write($htaccess);
	$File->close();
}

?>
