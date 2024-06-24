<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.18
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.0.18 アップデーター
 * 書き込み権限チェック
 */
$notWritablePath = [];
if(!is_writable(ROOT . DS . 'bin' . DS . 'cake.php')) {
    $notWritablePath[] = ROOT . DS . 'bin' . DS . 'cake.php';
}
if(!is_writable(ROOT . DS . 'config' . DS . 'app.php')) {
    $notWritablePath[] = ROOT . DS . 'config' . DS . 'app.php';
}
if(!is_writable(ROOT . DS . 'config' . DS . 'bootstrap.php')) {
    $notWritablePath[] = ROOT . DS . 'config' . DS . 'bootstrap.php';
}
if(!is_writable(ROOT . DS . 'config' . DS . 'bootstrap_cli.php')) {
    $notWritablePath[] = ROOT . DS . 'config' . DS . 'bootstrap_cli.php';
}
if(!is_writable(ROOT . DS . 'src' . DS . 'Application.php')) {
    $notWritablePath[] = ROOT . DS . 'src' . DS . 'Application.php';
}
if(!is_writable(ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php')) {
    $notWritablePath[] = ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php';
}
if(!is_writable(ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php')) {
    $notWritablePath[] = ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php';
}
if(!is_writable(ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php')) {
    $notWritablePath[] = ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php';
}
if(!is_writable(ROOT . DS . 'webroot' . DS . 'index.php')) {
    $notWritablePath[] = ROOT . DS . 'webroot' . DS . 'index.php';
}
if(!is_writable(ROOT . DS . 'config')) {
    $notWritablePath[] = ROOT . DS . 'config';
}
if($notWritablePath) {
    return [
        'updateMessage' => "アップデートを実行する前に次のファイルみ書き込み権限を与えてください<br>" . implode('<br>', $notWritablePath)
    ];
} else {
    return [];
}
