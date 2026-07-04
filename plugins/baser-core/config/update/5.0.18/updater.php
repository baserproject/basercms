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
 *
 * baserCMS5.1系対応の準備処理を実行
 */
use BaserCore\Utility\BcUpdateLog;

$updateDir = __DIR__;

if (is_writable(ROOT . DS . 'bin' . DS . 'cake.php')) {
    copy($updateDir . DS . 'bin' . DS . 'cake.php', ROOT . DS . 'bin' . DS . 'cake.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'bin' . DS . 'cake.php', $updateDir . DS . 'bin' . DS . 'cake.php'));
}
if (is_writable(ROOT . DS . 'config' . DS . 'app.php')) {
    copy($updateDir . DS . 'config' . DS . 'app.php', ROOT . DS . 'config' . DS . 'app.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'config' . DS . 'app.php', $updateDir . DS . 'config' . DS . 'app.php'));
}
if (is_writable(ROOT . DS . 'config' . DS . 'bootstrap.php')) {
    copy($updateDir . DS . 'config' . DS . 'bootstrap.php', ROOT . DS . 'config' . DS . 'bootstrap.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'config' . DS . 'bootstrap.php', $updateDir . DS . 'config' . DS . 'bootstrap.php'));
}
if (is_writable(ROOT . DS . 'config' . DS . 'bootstrap_cli.php')) {
    copy($updateDir . DS . 'config' . DS . 'bootstrap_cli.php', ROOT . DS . 'config' . DS . 'bootstrap_cli.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'config' . DS . 'bootstrap_cli.php', $updateDir . DS . 'config' . DS . 'bootstrap_cli.php'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'Application.php')) {
    copy($updateDir . DS . 'src' . DS . 'Application.php', ROOT . DS . 'src' . DS . 'Application.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'src' . DS . 'Application.php', $updateDir . DS . 'src' . DS . 'Application.php'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php')) {
    copy($updateDir . DS . 'src' . DS . 'Controller' . DS . 'AppController.php', ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'src' . DS . 'Controller' . DS . 'AppController.php', $updateDir . DS . 'src' . DS . 'Controller' . DS . 'AppController.php'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php')) {
    copy($updateDir . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php', ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php', $updateDir . DS . 'src' . DS . 'Controller' . DS . 'ErrorController.php'));
}
if (is_writable(ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php')) {
    copy($updateDir . DS . 'src' . DS . 'View' . DS . 'AjaxView.php', ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php', $updateDir . DS . 'src' . DS . 'View' . DS . 'AjaxView.php'));
}
if (is_writable(ROOT . DS . 'webroot' . DS . 'index.php')) {
    copy($updateDir . DS . 'webroot' . DS . 'index.php', ROOT . DS . 'webroot' . DS . 'index.php');
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'webroot' . DS . 'index.php', $updateDir . DS . 'webroot' . DS . 'index.php'));
}
if (is_writable(ROOT . DS . 'config')) {
    if(file_exists(ROOT . DS . 'config' . DS . 'plugins.php')) {
        if (is_writable(ROOT . DS . 'config' . DS . 'plugins.php')) {
            copy($updateDir . DS . 'config' . DS . 'plugins.php', ROOT . DS . 'config' . DS . 'plugins.php');
        } else {
            BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'config' . DS . 'plugins.php', $updateDir . DS . 'config' . DS . 'plugins.php'));
        }
    } else {
        copy($updateDir . DS . 'config' . DS . 'plugins.php', ROOT . DS . 'config' . DS . 'plugins.php');
    }
} else {
    BcUpdateLog::set(__d('baser_core', '{0} に書き込み権限がありません。{1} をコピーして手動で上書きしてください。', ROOT . DS . 'config', $updateDir . DS . 'config' . DS . 'plugins.php'));
}
