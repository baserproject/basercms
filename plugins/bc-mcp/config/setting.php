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
         *
         * Cookie を使わないエンドポイントのみを列挙する。ワイルドカードで
         * /bc-mcp/oauth2/* を一括指定すると、管理画面セッションで動作する
         * 同意エンドポイント（authorize）まで無防備になるため、authorize は
         * 意図的に含めない。
         */
        'skipCsrfUrl' => [
            'Mcp' => '/bc-mcp',
            'OAuth2Token' => '/bc-mcp/oauth2/token',
            'OAuth2Register' => '/bc-mcp/oauth2/register',
            'OAuth2RegisterClient' => '/bc-mcp/oauth2/register/*',
            'OAuth2Verify' => '/bc-mcp/oauth2/verify',
            'OAuth2ClientInfo' => '/bc-mcp/oauth2/client-info',
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
    'Cache' => [
        'bc_mcp_registration' => [
            'className' => \Cake\Cache\Engine\FileEngine::class,
            'duration' => '+1 hours',
            'path' => CACHE . 'bc_mcp' . DS,
            'prefix' => 'bc_mcp_registration_',
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
        ],
        /**
         * 動的クライアント登録の制限
         *
         * maxPerHour: 同一IPから1時間に登録できる件数
         * unusedClientRetentionDays: 一度も認可に使われないクライアントの保持日数
         */
        'registration' => [
            'maxPerHour' => 10,
            'unusedClientRetentionDays' => 30,
        ],
    ]
];
