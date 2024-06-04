<?php
/**
 * 5.0.16 アップデーター
 *
 * baserCMS5.1系対応の準備処理を実行
 */
use BaserCore\Utility\BcUpdateLog;

$updateDir = __DIR__;

if (is_writable(ROOT . DS . 'bin' . DS . 'cake.php')) {
    copy($updateDir . DS . 'bin' . DS . 'cake.php', ROOT . DS . 'bin' . DS . 'cake.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'bin' . DS . 'cake.php に書き込み権限がありません。' . $updateDir . 'bin' . DS . 'cake.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'config' . DS . 'app.php')) {
    copy($updateDir . DS . 'config' . DS . 'app.php', ROOT . DS . 'config' . DS . 'app.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'config' . DS . 'app.php に書き込み権限がありません。' . $updateDir . DS . 'config' . DS . 'app.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'config' . DS . 'bootstrap.php')) {
    copy($updateDir . DS . 'config' . DS . 'bootstrap.php', ROOT . DS . 'config' . DS . 'bootstrap.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'config' . DS . 'bootstrap.php に書き込み権限がありません。' . $updateDir . DS . 'config' . DS . 'bootstrap.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'config' . DS . 'bootstrap_cli.php')) {
    copy($updateDir . DS . 'config' . DS . 'bootstrap_cli.php', ROOT . DS . 'config' . DS . 'bootstrap_cli.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'config' . DS . 'bootstrap_cli.php に書き込み権限がありません。' . $updateDir . DS . 'config' . DS . 'bootstrap_cli.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'Application.php')) {
    copy($updateDir . DS . 'src' . DS . 'Application.php', ROOT . DS . 'src' . DS . 'Application.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'src' . DS . 'Application.php に書き込み権限がありません。' . $updateDir . DS . 'src' . DS . 'Application.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php')) {
    copy($updateDir . DS . 'src' . DS . 'Controller' . DS . 'AppController.php', ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php に書き込み権限がありません。' . $updateDir . DS . 'src' . DS . 'Controller' . DS . 'AppController.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php')) {
    copy($updateDir . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php', ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php に書き込み権限がありません。' . $updateDir . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php')) {
    copy($updateDir . DS . 'src' . DS . 'View' . DS . 'AjaxView.php', ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php に書き込み権限がありません。' . $updateDir . DS . 'src' . DS . 'View' . DS . 'AjaxView.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'webroot' . DS . 'index.php')) {
    copy($updateDir . DS . 'webroot' . DS . 'index.php', ROOT . DS . 'webroot' . DS . 'index.php');
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'webroot' . DS . 'index.php に書き込み権限がありません。' . $updateDir . DS . 'webroot' . DS . 'index.php をコピーして手動で上書きしてください。'));
}
if (is_writable(ROOT . DS . 'config')) {
    if (is_writable(ROOT . DS . 'config' . DS . 'plugins.php')) {
        copy($updateDir . DS . 'config' . DS . 'plugins.php', ROOT . DS . 'config' . DS . 'plugins.php');
    } else {
        BcUpdateLog::set(__d('baser_core', ROOT . DS . 'config' . DS . 'plugins.php に書き込み権限がありません。' . $updateDir . DS . 'config' . DS . 'plugins.php をコピーして手動で上書きしてください。'));
    }
} else {
    BcUpdateLog::set(__d('baser_core', ROOT . DS . 'config に書き込み権限がありません。' . $updateDir . DS . 'config' . DS . 'plugins.php をコピーして、手動で' . ROOT . DS . 'config 配下に配置してください。'));
}
