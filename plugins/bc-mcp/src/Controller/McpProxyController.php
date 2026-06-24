<?php
declare(strict_types=1);

namespace BcMcp\Controller;

use BaserCore\Controller\AppController;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\ServiceUnavailableException;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Hash;
use BcMcp\Mcp\McpServerManger;
use BcMcp\Mcp\PermissionManager;
use BcMcp\OAuth2\Service\OAuth2Service;

/**
 * MCPサーバーへのプロキシコントローラー
 * SSEクライアントとしてMCPサーバーと通信し、HTTPリクエストをMCPプロトコルに変換
 * OAuth2認証対応
 */
class McpProxyController extends AppController
{
    /**
     * OAuth2サービス
     *
     * @var OAuth2Service
     */
    private OAuth2Service $oauth2Service;

    private McpServerManger $mcpServerManager;

    /**
     * 初期化
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->FormProtection->setConfig('validate', false);
        // OAuth2サービスを初期化
        $this->oauth2Service = new OAuth2Service();
        $this->mcpServerManager = new McpServerManger();

        // CORS設定（統一された設定）
        $this->response = $this->response->withHeader('Access-Control-Allow-Origin', '*');
        $this->response = $this->response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response = $this->response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, MCP-Protocol-Version');
    }

    /**
     * MCPプロトコルバージョンの取得
     * @return string
     */
    private function getProtocolVersion(): string
    {
        $protocolVersion = $this->request->getHeaderLine('MCP-Protocol-Version');
        if (!empty($protocolVersion)) {
            return $protocolVersion;
        }
        $requestBody = (string)$this->request->getBody();
        $mcpRequest = json_decode($requestBody, true);

        if (isset($mcpRequest['params']['protocolVersion'])) {
            return $mcpRequest['params']['protocolVersion'];
        }
        return '2025-06-18';
    }

    /**
     * リクエスト処理前の認証チェック
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $method = $this->request->getMethod();

        // OPTIONS は認証不要
        if ($method === 'OPTIONS') {
            return;
        }

        $response = $this->validateOAuth2Token();
        if ($response) {
            $event->setResult($response);
            return;
        }
    }

    /**
     * OAuth2トークンの検証
     */
    private function validateOAuth2Token(): Response|null
    {
        $authHeader = $this->request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->returnUnauthorizedResponse('Missing or invalid authorization header');
        }

        $token = substr($authHeader, 7);
        $tokenData = $this->oauth2Service->validateAccessToken($token);

        if (!$tokenData) {
            return $this->returnUnauthorizedResponse('Invalid or expired access token');
        }

        // トークン情報をリクエストに保存
        $this->request = $this->request
            ->withAttribute('oauth_client_id', $tokenData['client_id'])
            ->withAttribute('oauth_user_id', $tokenData['user_id'])
            ->withAttribute('oauth_scopes', $tokenData['scope']);
        return null;
    }

    /**
     * 認証エラーのレスポンスを返す
     * @param string $message
     * @return Response
     */
    private function returnUnauthorizedResponse(string $message): \Cake\Http\Response
    {
        $siteUrl = rtrim((string)env('SITE_URL', 'https://localhost'), '/');
        $resourceMetadataUrl = $siteUrl . '/.well-known/oauth-protected-resource/bc-mcp';

        $wwwAuthenticate = sprintf(
            'Bearer resource_metadata="%s"',
            $resourceMetadataUrl
        );

        return $this->response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Cache-Control', 'no-store')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('WWW-Authenticate', $wwwAuthenticate)
            ->withStringBody(json_encode([
                'error' => 'invalid_client',
                'message' => $message
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * MCPサーバーへのプロキシ処理
     * /mcp へのアクセスを内部MCPサーバーに転送
     * OPTIONSリクエストも含めて全てここで処理
     */
    public function index()
    {
        $protocolVersion = $this->getProtocolVersion();
        $this->response = $this->response->withHeader('MCP-Protocol-Version', $protocolVersion);

        // OPTIONSリクエストの場合はCORSレスポンスを返す
        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->_handleOptionsRequest();
        }

        // POST以外のメソッドは許可しない
        if ($this->request->getMethod() === 'GET') {
            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'name' => 'bc-mcp',
                    'version' => '1.0.0',
                    'authenticated' => true
                ]));
        }

        try {
            // MCPサーバーの設定を取得
            $config = $this->mcpServerManager->getServerConfig();

            // MCPサーバーが起動しているかチェック
            if (!$this->mcpServerManager->isServerRunning()) {
                throw new ServiceUnavailableException(
                    'MCPサーバーが起動していません。管理画面からMCPサーバーを起動してください。'
                );
            }

            // CakePHPのリクエストオブジェクトからJSONボディを取得
            $requestBody = (string)$this->request->getBody();

            if (empty($requestBody)) {
                // 空ボディは不正
                return $this->response->withStatus(400);
            }

            // JSONをパースしてMCPリクエストを検証
            $mcpRequest = json_decode($requestBody, true);
            if (!$mcpRequest || !isset($mcpRequest['jsonrpc']) || $mcpRequest['jsonrpc'] !== '2.0') {
                throw new BadRequestException('Invalid MCP request format');
            }

            $mcpRequest['params']['arguments']['loginUserId'] = $this->request->getAttribute('oauth_user_id');

            if(!$this->checkPermission($mcpRequest)) {
                return $this->response
                    ->withStatus(403)
                    ->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'jsonrpc' => '2.0',
                        'error' => [
                            'code' => 403,
                            'message' => 'Forbidden: You do not have permission to perform this action.'
                        ]
                    ]));
            }

            // SSEクライアントとしてMCPサーバーに接続してリクエストを処理
            $response = $this->sendMcpRequest($config, $mcpRequest);

            $this->response = $this->response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withStringBody(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            if ($this->request->getData('method') === 'notifications/initialized') {
                $this->response = $this->response->withStatus(202);
            }
        } catch (BadRequestException $e) {
            throw $e;
        } catch (ForbiddenException $e) {
            return $this->response
                ->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => 403,
                        'message' => 'MCPサーバーとの通信に失敗しました: ' . $e->getMessage()
                    ]
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => 500,
                        'message' => 'MCPサーバーとの通信に失敗しました: ' . $e->getMessage()
                    ]
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return $this->response;
    }

    /**
     * 権限チェック
     * @param array $mcpRequest
     * @return bool
     */
    public function checkPermission(array $mcpRequest): bool
    {
        if($mcpRequest['method'] !== 'tools/call') return true;

        if (!filter_var(env('USE_CORE_ADMIN_API', false), FILTER_VALIDATE_BOOLEAN)) {
            throw new ForbiddenException(__d('baser_core', 'baser Admin APIは許可されていません。'));
        }

        /** @var UsersService $usersService */
        $usersService = $this->getService(UsersServiceInterface::class);
        $user = $usersService->get($mcpRequest['params']['arguments']['loginUserId']);
        if(!$user) return false;
        if (BcUtil::isAdminUser($user)) {
            return true;
        }
        $userGroupsIds = Hash::extract($user->toArray()['user_groups'], '{n}.id');
        $permissionManager = new PermissionManager();
        return $permissionManager->checkPermission(
            $mcpRequest['params']['name'],
            $userGroupsIds,
            $mcpRequest['params']['arguments']
        );
    }

    /**
     * StreamableHttpServerTransport用のMCPリクエスト送信
     * 直接JSONエンドポイントとして通信（SSE初期化不要）
     */
    private function sendMcpRequest(array $config, array $mcpRequest): array
    {
        // StreamableHttpServerTransportの場合はルートパス（/）を使用
        $jsonUrl = "http://127.0.0.1:{$config['port']}/";

        try {
            $client = new Client(['timeout' => 10]);
            $response = $client->post($jsonUrl, json_encode($mcpRequest), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (!$responseData) {
                return [
                    "jsonrpc" => "2.0",
                    "result" => []
                ];
            }

            // MCP Inspector対応：プロトコルバージョンとcapabilitiesを調整
            if (isset($responseData['result']) && isset($mcpRequest['method']) && $mcpRequest['method'] === 'initialize') {
                // capabilitiesにツールの存在を示す（実際のツールリストはtools/listで取得）
                $responseData['result']['capabilities'] = [
                    'tools' => ['listChanged' => true],  // 空オブジェクトでツール機能があることを示す
                    'resources' => ['listChanged' => true],
                    'prompts' => ['listChanged' => true]
                ];
                $responseData['result']['protocolVersion'] = '2025-06-18';
            }
            return $responseData;

        } catch (\Exception $e) {
            throw new \Exception('MCPサーバーとの通信に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * OPTIONSリクエストの処理（CORS プリフライト対応）
     */
    private function _handleOptionsRequest()
    {
        $this->response = $this->response
            ->withHeader('Access-Control-Max-Age', '86400')
            ->withStatus(200);
        return $this->response;
    }

    /**
     * OPTIONSリクエストの処理（CORS プリフライト対応）
     * 後方互換性のため残しているが、実際は_handleOptionsRequestが使用される
     */
    public function options()
    {
        return $this->_handleOptionsRequest();
    }

}
