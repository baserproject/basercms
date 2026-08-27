<?php
declare(strict_types=1);

namespace BcMcp\Controller;

use BaserCore\Controller\AppController;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Hash;
use BcMcp\Mcp\McpContext;
use BcMcp\Mcp\McpRequestHandler;
use BcMcp\Mcp\NegotiationLogger;
use BcMcp\Mcp\PermissionManager;
use BcMcp\OAuth2\Exception\OAuth2ConfigurationException;
use BcMcp\OAuth2\Service\OAuth2Service;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * MCPサーバーのリクエスト受け口
 *
 * 常駐プロセスを持たず、CakePHP のリクエスト内で SDK を実行する。
 * 本コントローラーの責務は認証・認可と、CakePHP のリクエスト／レスポンスと
 * SDK の HttpMessage の相互変換に限られる。プロトコルの世代判定・
 * server/discover・必須ヘッダ検証・resultType やキャッシュヒントの付与は
 * すべて SDK が担うため、応答の内容には手を加えない。
 */
class McpProxyController extends AppController
{
    /**
     * OAuth2サービス
     *
     * @var OAuth2Service|null
     */
    private ?OAuth2Service $oauth2Service = null;

    /**
     * OAuth2 の設定不備
     *
     * @var OAuth2ConfigurationException|null
     */
    private ?OAuth2ConfigurationException $oauth2ConfigError = null;

    /**
     * 初期化
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->FormProtection->setConfig('validate', false);
        // OAuth2サービスを初期化
        try {
            $this->oauth2Service = new OAuth2Service();
        } catch (OAuth2ConfigurationException $e) {
            // 設定不備は beforeFilter で 503 として返す
            $this->oauth2ConfigError = $e;
        }

        // CORS設定。許可したオリジンのみを返す（ワイルドカードは使わない）
        $origin = $this->request->getHeaderLine('Origin');
        if ($origin !== '' && $this->isAllowedOrigin($origin)) {
            $this->response = $this->response->withHeader('Access-Control-Allow-Origin', $origin);
        }
        $this->response = $this->response->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response = $this->response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, MCP-Protocol-Version, Mcp-Method, Mcp-Name, Mcp-Session-Id');
    }

    /**
     * リクエスト処理前の認証チェック
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        if ($event->getResult()) return;

        // 暗号化キー未設定などの設定不備は、認証より前に 503 として返す
        if ($this->oauth2ConfigError) {
            $event->setResult($this->response
                ->withStatus(503)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => -32603,
                        'message' => $this->oauth2ConfigError->getMessage(),
                    ]
                ], JSON_UNESCAPED_UNICODE)));
            return;
        }

        $method = $this->request->getMethod();

        // Origin 検証はトランスポートレベルの要件であり、認証より前に効かせる。
        // ブラウザから送信された Origin のみが対象で、サーバー間通信のように
        // Origin を持たないリクエストは検証しない。
        $origin = $this->request->getHeaderLine('Origin');
        if ($origin !== '' && !$this->isAllowedOrigin($origin)) {
            $event->setResult($this->returnForbiddenOriginResponse());
            return;
        }

        // OPTIONS は認証不要
        if ($method === 'OPTIONS') {
            return;
        }

        // 2026-07-28 では GET ストリームとセッションの DELETE が廃止されている。
        // メソッド自体が許可されないため認証より前に応答する。
        if (in_array($method, ['GET', 'DELETE'], true)) {
            $event->setResult($this->returnMethodNotAllowedResponse());
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
     * Origin が許可されているかを判定する
     *
     * Streamable HTTP の MUST 要件。悪意あるサイトが DNS リバインディングにより
     * ローカルの MCP サーバーを操作するのを防ぐ。
     *
     * @param string $origin Origin ヘッダの値
     * @return bool
     */
    public function isAllowedOrigin(string $origin): bool
    {
        $allowed = (array)Configure::read('BcMcp.allowedOrigins', []);
        if (!$allowed) {
            // 設定が無い場合は自サイトのオリジンのみを許可する
            $siteUrl = rtrim((string)env('SITE_URL', ''), '/');
            if ($siteUrl) {
                $parts = parse_url($siteUrl);
                if (!empty($parts['scheme']) && !empty($parts['host'])) {
                    $allowed = [
                        $parts['scheme'] . '://' . $parts['host']
                        . (isset($parts['port'])? ':' . $parts['port'] : '')
                    ];
                }
            }
        }
        // 部分一致で通さないよう厳密に比較する
        return in_array($origin, $allowed, true);
    }

    /**
     * 許可されない Origin のレスポンスを返す
     *
     * @return Response
     */
    private function returnForbiddenOriginResponse(): Response
    {
        return $this->response
            ->withStatus(403)
            ->withHeader('Content-Type', 'application/json')
            ->withStringBody(json_encode([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32600,
                    'message' => 'Forbidden: invalid Origin.'
                ]
            ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * 許可されないメソッドのレスポンスを返す
     *
     * @return Response
     */
    private function returnMethodNotAllowedResponse(): Response
    {
        return $this->response
            ->withStatus(405)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Allow', 'POST, OPTIONS')
            ->withStringBody(json_encode([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32601,
                    'message' => 'Method not allowed. Use POST.'
                ]
            ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * MCP リクエストの受け口
     *
     * /bc-mcp へのアクセスを同一プロセス内の MCP サーバーで処理する。
     * OPTIONS リクエストも含めて全てここで処理する。
     */
    public function index()
    {
        // OPTIONSリクエストの場合はCORSレスポンスを返す
        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->_handleOptionsRequest();
        }

        try {
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

            // クライアントの世代とプロトコルバージョンを記録する。
            // クライアント側が Modern へ移行した事を検知できるようにするため。
            NegotiationLogger::log($mcpRequest, $this->request->getHeaderLine('MCP-Protocol-Version'));

            // 認証済みの操作者をコンテキストに設定する。
            // リクエストボディへ注入しないのは、2026-07-28 でヘッダとボディの
            // 一致が検証されるため。
            McpContext::setLoginUserId((int)$this->request->getAttribute('oauth_user_id'));

            if (!$this->checkPermission($mcpRequest)) {
                return $this->response
                    ->withStatus(403)
                    ->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'jsonrpc' => '2.0',
                        'error' => [
                            'code' => 403,
                            'message' => 'Forbidden: You do not have permission to perform this action.'
                        ]
                    ], JSON_UNESCAPED_UNICODE));
            }

            $mcpResponse = (new McpRequestHandler())->handle($this->toMcpMessage($mcpRequest));

            $response = $this->response
                ->withStatus($mcpResponse->getStatusCode())
                ->withStringBody((string)$mcpResponse->getBody());
            foreach($mcpResponse->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            return $response;
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
                        'message' => $e->getMessage()
                    ]
                ], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => 500,
                        'message' => 'MCPリクエストの処理に失敗しました: ' . $e->getMessage()
                    ]
                ], JSON_UNESCAPED_UNICODE));
        } finally {
            McpContext::clear();
        }
    }

    /**
     * CakePHP のリクエストを SDK の HttpMessage に変換する
     *
     * 2026-07-28 では MCP-Protocol-Version / Mcp-Method / Mcp-Name が必須ヘッダで、
     * SDK がヘッダとボディの一致を検証する。クライアントが送ってきたヘッダを
     * そのまま引き継ぎ、ボディも改変しない事で整合性を保つ。
     * Authorization は認証がプロキシで完結しているため渡さない。
     *
     * @param array $mcpRequest MCP リクエスト
     * @return \Mcp\Server\Transport\Http\HttpMessage
     */
    public function toMcpMessage(array $mcpRequest): HttpMessage
    {
        $message = new HttpMessage(json_encode($mcpRequest, JSON_UNESCAPED_UNICODE));
        $message->setMethod($this->request->getMethod());
        $message->setUri('/bc-mcp');
        $message->setHeader('Content-Type', 'application/json');
        $message->setHeader('Accept', 'application/json, text/event-stream');

        // Mcp-Session-Id は Legacy 世代（initialize 方式）のクライアントが
        // セッションを維持するために使う。Modern では廃止されているが、
        // Dual-era サーバーとして両方に応じるため透過する。
        $targets = ['MCP-Protocol-Version', 'Mcp-Method', 'Mcp-Name', 'Mcp-Session-Id', 'Last-Event-ID'];
        foreach($targets as $target) {
            $value = $this->request->getHeaderLine($target);
            if ($value !== '') {
                $message->setHeader($target, $value);
            }
        }
        // x-mcp-header 由来の Mcp-Param-* も引き継ぐ
        foreach($this->request->getHeaders() as $name => $values) {
            if (stripos($name, 'Mcp-Param-') === 0) {
                $message->setHeader($name, implode(', ', $values));
            }
        }
        return $message;
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
        $user = $usersService->get(McpContext::getLoginUserId());
        if(!$user) return false;
        if (BcUtil::isAdminUser($user)) {
            return true;
        }
        $userGroupsIds = Hash::extract($user->toArray()['user_groups'], '{n}.id');
        $permissionManager = new PermissionManager();
        return $permissionManager->checkPermission(
            $mcpRequest['params']['name'],
            $userGroupsIds,
            $mcpRequest['params']['arguments'] ?? []
        );
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
