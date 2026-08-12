<?php
declare(strict_types=1);

use Cake\Log\Engine\FileLog;

return [
    'BcApp' => [
        /**
         * System Navigation
         */
        'adminNavigation' => [
            'Systems' => [
                'BcMcpServerManager' => [
                    'title' => 'MCPサーバー管理',
                    'type' => 'system',
                    'url' => [
                        'prefix' => 'Admin',
                        'plugin' => 'BcMcp',
                        'controller' => 'McpServerManager',
                        'action' => 'index'
                    ],
                    'currentRegex' => '/\/bc-mcp\/admin\/mcp-server-manager.*/',
                ],
            ]
        ],
        /**
         * CSRFチェックをスキップするURL
         */
        'skipCsrfUrl' => [
            'Mcp' => '/bc-mcp',
            // RFC 7591 動的クライアント登録プロトコル（ワイルドカードパターン使用）
            'OAuth2All' => '/bc-mcp/oauth2/*',
            'OAuth2AdminAll' => '/baser/admin/bc-mcp/oauth2/*'
        ]
    ],
    'BcPermission' => [
        /**
         * デフォルトで許可するURL
         */
        'defaultAllows' => [
            'Authorize' => '/bc-mcp/oauth2/authorize'
        ]
    ],
    'Log' => [
        'mcp' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'mcp',
            'scopes' => ['mcp'],
            'levels' => ['info', 'error']
        ]
    ],
    'BcMcp' => [
        /**
         * Origin ヘッダの許可リスト
         *
         * DNS リバインディング攻撃対策として、ブラウザから送信された Origin を
         * 検証する。Streamable HTTP の MUST 要件。
         * 空配列の場合は自サイトのオリジン（SITE_URL）のみを許可する。
         * Origin ヘッダを持たないリクエスト（サーバー間通信）は検証対象外。
         */
        'allowedOrigins' => [],
        /**
         * 利用可能なMCPサーバー
         */
        'availableServers' => [
            'BaserCore' => \BcMcp\Mcp\BaserCore\BaserCoreServer::class,
            'BcBlog' => \BcMcp\Mcp\BcBlog\BcBlogServer::class,
            'BcCustomContent' => \BcMcp\Mcp\BcCustomContent\BcCustomContentServer::class,
        ]
    ]
];
