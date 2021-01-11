<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * タイムゾーンを設定
 */
ini_set('date.timezone', 'Asia/Tokyo');
@putenv("TZ=JST-9");

/**
 * 実行ファイル名を取得
 */
$fileName = str_replace('/', DS, $_SERVER['SCRIPT_FILENAME']);

/**
 * ROOT
 *
 * チカッパレンタルサーバでは、DocumentoRoot のひとつ上の階層に配置された cake を
 * ターゲットとして ROOT を決定したため、うまく動作しなかったため、「app内にcakeを配置」 パターンは停止
 */
// 通常パターン
if (@is_dir(dirname(dirname(dirname($fileName))) . DS . 'lib' . DS . 'Cake')) {
	define('ROOT', dirname(dirname(dirname($fileName))));

// app内にcakeを配置 パターン
/*}elseif(is_dir(dirname(dirname($fileName)).DS.'cake')){
	define('ROOT', dirname(dirname($fileName)));*/

// WEBROOT配置パターン
} elseif (is_dir(dirname($fileName) . DS . 'lib' . DS . 'Cake')) {
	define('ROOT', dirname($fileName));
}

/**
 * APP_DIR
 *
 * 「WEBROOT配置パターン」に対応するため app 固定とする
 */
define('APP_DIR', 'app');

/**
 * WEBROOT_DIR
 *
 * 「WEBROOT配置パターン」に対応するため webroot 固定とする
 */
define('WEBROOT_DIR', 'webroot');

/**
 * WWW_ROOT
 */
define('WWW_ROOT', dirname($fileName) . DS);
