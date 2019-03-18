<?php
/**
 * タイムゾーンを設定する
 */
ini_set('date.timezone', 'Asia/Tokyo');
@putenv("TZ=JST-9");

// CUSTOMIZE ADD 2012/10/27 ryuring
/**
 * 実行ファイル名を取得する
 */
$fileName = $_SERVER['SCRIPT_FILENAME'];

// CUSTOMIZE ADD 2016/08/30 katokaisya
$fileName = str_replace('/', DS, $fileName);

// CUSTOMIZE MODIFY 2012/10/27 ryuring
// 基本的には、cake が配置されているディレクトリをROOTとみなす。
//define('ROOT', dirname(dirname(dirname(__FILE__))));
// ---

/* 通常パターン */
if (@is_dir(dirname(dirname(dirname($fileName))) . DS . 'lib' . DS . 'Cake')) {
    define('ROOT', dirname(dirname(dirname($fileName))));
// app内にcakeを配置
// チカッパでは、DocumentoRoot のひとつ上の階層に配置されたcakeを
// ターゲットとして ROOT を決定したため、うまく動作しなかった。
/*}elseif(is_dir(dirname(dirname($fileName)).DS.'cake')){
    define('ROOT', dirname(dirname($fileName)));*/

// WEBROOT配置
} elseif (is_dir(dirname($fileName) . DS . 'lib' . DS . 'Cake')) {
    define('ROOT', dirname($fileName));
}

// CUSTOMIZE MODIFY 2012/10/27 ryuring
// app ディレクトリは「WEBROOT配置」の絡みがあるので[app]固定とする
// app ディレクトリの名称を変更する場合は、以下を変更する。
//define('APP_DIR', basename(dirname(dirname(__FILE__))));
// ---
define('APP_DIR', 'app');

// CUSTOMIZE MODIFY 2014/03/23 ryuring
// webroot 配置の絡みがあるので webroot 固定とする
//define('WEBROOT_DIR', basename(dirname(__FILE__)));
// ---
define('WEBROOT_DIR', 'webroot');

// CUSTOMIZE MODIFY 201X/XX/XX ryuring
//define('WWW_ROOT', dirname(__FILE__) . DS);
// ---
define('WWW_ROOT', dirname($fileName) . DS);
