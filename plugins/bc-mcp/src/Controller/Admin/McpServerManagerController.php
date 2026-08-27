<?php
declare(strict_types=1);

namespace BcMcp\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcMcp\Mcp\McpRequestHandler;
use BcMcp\Mcp\NegotiationLogger;
use Cake\Routing\Router;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * MCPサーバー管理コントローラー
 *
 * 常駐プロセスを持たないため、死活監視や起動・停止ではなく、接続情報・
 * 提供しているツール・直近の接続状況を表示する。
 */
class McpServerManagerController extends BcAdminAppController
{

    /**
     * 初期化
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->set('title', 'MCPサーバー管理');
    }

    /**
     * MCPサーバー情報
     */
    public function index()
    {
        $baseUrl = rtrim(Router::url('/', true), '/');

        $this->set([
            'endpointUrl' => $baseUrl . '/bc-mcp',
            'authorizationServerMetadataUrl' => $baseUrl . '/.well-known/oauth-authorization-server/bc-mcp',
            'protectedResourceMetadataUrl' => $baseUrl . '/.well-known/oauth-protected-resource/bc-mcp',
            'protocolVersions' => ['2026-07-28', '2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'],
            'tools' => $this->getRegisteredTools(),
            'negotiations' => NegotiationLogger::readRecent(10),
            // 暗号化キーが無いと OAuth2/MCP は 503 で停止するため、
            // 管理画面で気付けるようにする
            'encryptionKeyMissing' => !env('OAUTH2_ENC_KEY'),
        ]);
    }

    /**
     * 登録済みツールを取得する
     *
     * SDK はツール一覧を取得する API を持たないため、本番と同じ経路で
     * tools/list を実行して取得する。テンプレートへの手書きをやめる事で、
     * ツールを追加すれば表示にも反映される。
     *
     * @return array ツールの配列（name / description を含む）
     */
    public function getRegisteredTools(): array
    {
        $request = new HttpMessage(json_encode([
            'jsonrpc' => '2.0',
            'id' => 'admin-tools-list',
            'method' => 'tools/list',
            'params' => [
                '_meta' => [
                    'io.modelcontextprotocol/protocolVersion' => '2026-07-28',
                    'io.modelcontextprotocol/clientInfo' => [
                        'name' => 'baserCMS Admin',
                        'version' => '1.0.0',
                    ],
                    'io.modelcontextprotocol/clientCapabilities' => [],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE));
        $request->setMethod('POST');
        $request->setUri('/bc-mcp');
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Accept', 'application/json');
        $request->setHeader('MCP-Protocol-Version', '2026-07-28');
        $request->setHeader('Mcp-Method', 'tools/list');

        $response = (new McpRequestHandler())->handle($request);
        $decoded = json_decode((string)$response->getBody(), true);

        return $decoded['result']['tools'] ?? [];
    }

}
