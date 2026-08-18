<?php
declare(strict_types=1);

use BaserCore\Utility\BcUtil;

$message = [];
if(!is_writable(CONFIG)) $message[] = CONFIG . ' に書き込み権限がありません。インストールする前に書き込み権限を与えてください。';
if(!is_writable(CONFIG . '.env')) $message[] = CONFIG . '.env に書き込み権限がありません。インストールする前に書き込み権限を与えてください。';
if(BcUtil::verpoint('5.1.10') > BcUtil::verpoint(BcUtil::getVersion())) {
    $message[] = 'baserCMSのバージョンが5.1.10未満です。baserCMSを5.1.10以上にアップデートしてからインストールしてください。';
}
$message[] = 'インストール時には、認証必要領域の Web API（baser Admin Api）を有効を有効化します。';

return [
    'type' => 'Plugin',
    'title' => 'baserCMS MCP Server',
    'description' => 'baserCMSをAIエージェントから操作するためのMCPサーバーを提供します。',
    'author' => 'baserCMS User Community',
    'url' => 'https://basercms.net',
    'installMessage' =>implode("<br>", $message),
    'adminLink' => [
        'plugin' => 'BcMcp',
        'controller' => 'McpServerManager',
        'action' => 'index'
    ],
];
