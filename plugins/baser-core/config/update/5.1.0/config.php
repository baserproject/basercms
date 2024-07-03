<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.19
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * 5.1.0 アップデーター
 */
$notWritablePath = [];
if(!is_writable(ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php')) {
    $notWritablePath[] = ROOT . DS . 'src' . DS . 'View' . DS . 'AjaxView.php';
}
$message = '';
if(\Cake\Core\Plugin::isLoaded('BcUpdateSupporter')) {
    $message = "アップデート前に、BcUpdateSupporterプラグインを無効化してください。\n";
}
$message .= "baserCMS 5.1.0 へのアップデートの際、プラグインに問題がある場合、アップデート完了後に画面が表示できなくなる可能性があります。\n" .
    "アップデート前に、コアプラグイン以外を一度無効化しておいてください。";
if($notWritablePath) {
    $message .= "\nアップデートを実行する前に次のファイルみ書き込み権限を与えてください<br>" . implode('<br>', $notWritablePath)
}
return [
    'updateMessage' => $message
];
