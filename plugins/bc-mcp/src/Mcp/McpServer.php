<?php
declare(strict_types=1);

namespace BcMcp\Mcp;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Mcp\Server\McpServer as SdkMcpServer;

/**
 * baserCMS MCP Server
 *
 * baserCMSのデータを外部から操作するためのMCPサーバー
 * 各エンティティサーバーを統合して提供する
 *
 * プロトコルの世代判定・server/discover・必須ヘッダ検証・resultType や
 * キャッシュヒントの付与は SDK が担うため、本クラスの責務はツールの登録に絞る。
 */
class McpServer
{

    /**
     * SDK のサーバー
     * @var \Mcp\Server\McpServer
     */
    private SdkMcpServer $server;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->buildServer();
    }

    /**
     * サーバーのビルド
     */
    private function buildServer(): void
    {
        $this->server = new SdkMcpServer(
            'baserCMS MCP Server',
            new McpLogger(LOGS . 'bc_mcp_error.log'),
            '1.0.0'
        );

        $availableServers = Configure::read('BcMcp.availableServers', []);
        foreach($availableServers as $serverClass) {
            foreach($serverClass::getToolClasses() as $toolClass) {
                (new $toolClass())->registerTools($this->server);
            }
        }

        // サーバー情報ツールを追加
        $this->server->tool(
            name: 'serverInfo',
            description: 'サーバーのバージョンや環境情報を返します',
            callback: [$this, 'serverInfo'],
            inputSchema: [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'number', 'description' => 'ID'],
                ]
            ]
        );
    }

    /**
     * MCPサーバーの実体を取得する
     *
     * @return \Mcp\Server\McpServer
     */
    public function getServer(): SdkMcpServer
    {
        return $this->server;
    }

    /**
     * 標準入力からサーバーを起動する
     *
     * HTTP 経由の利用は /bc-mcp エンドポイントが担うため、常駐プロセスとしての
     * 起動は標準入出力のみを提供する。
     *
     * @return void
     */
    public function runStdio(): void
    {
        $this->server->runStdio();
    }

    /**
     * サーバー情報を取得する
     *
     * @param int|null $id ID
     * @return array
     */
    public function serverInfo(?int $id = null): array
    {
        return [
            'php_version' => PHP_VERSION,
            'basercms_version' => BcUtil::getVersion(),
            'cakephp_version' => Configure::version(),
            'server_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'mcp_server_version' => '1.0.0',
            'supported_clients' => ['ChatGPT', 'Claude', 'Custom MCP Clients'],
            'available_transports' => ['stdio', 'http'],
        ];
    }

}
