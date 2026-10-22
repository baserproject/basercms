<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * アクセスルール初期値
 *
 * MCP 経由のツール実行は、各プラグインの Web API のアクセスルールで判定される。
 * OAuth2 の認可エンドポイントは BcPermission.defaultAllows で許可している
 */

return [
    'permission' => [
        'McpServerManagerAdmin' => [
            'title' => __d('baser_core', 'MCPサーバー管理'),
            'plugin' => 'BcMcp',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', 'MCPサーバー情報'), 'url' => '/baser/admin/bc-mcp/mcp-server-manager', 'method' => '*', 'auth' => false],
                'All' => ['title' => __d('baser_core', '全て'), 'url' => '/baser/admin/bc-mcp/mcp_server_manager/*', 'method' => '*', 'auth' => false],
            ]
        ],
    ]
];
