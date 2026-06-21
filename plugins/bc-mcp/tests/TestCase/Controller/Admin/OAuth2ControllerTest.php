<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\McpServerManger;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Admin OAuth2Controller Test Case
 * 認証が必要なOAuth2エンドポイントのテスト
 */
class OAuth2ControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $_ENV['UNIT_TEST'] = true;

        $this->loadFixtureScenario(InitAppScenario::class);
        // OAuth2設定をセットアップ
        Configure::write('BcMcp.OAuth2.clients', [
            'mcp-client' => [
                'name' => 'MCP Server Client',
                'secret' => 'mcp-secret-key',
                'redirect_uris' => ['http://localhost'],
                'grants' => ['authorization_code'],
                'scopes' => ['read', 'write']
            ]
        ]);

        Configure::write('BcMcp.OAuth2.scopes', [
            'read' => 'データの読み取り',
            'write' => 'データの書き込み',
            'admin' => '管理者権限'
        ]);

        Configure::write('OAuth2.accessTokenTTL', 'PT1H');

        // テスト用のOAuth2キーペアが存在することを確認
        $privateKeyPath = CONFIG . 'oauth2_private.key';
        $publicKeyPath = CONFIG . 'oauth2_public.key';

        if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath)) {
            $this->generateTestKeys($privateKeyPath, $publicKeyPath);
        }

        // Admin配下のテスト用設定
        $this->configRequest([
            'environment' => [
                'HTTPS' => 'off'
            ]
        ]);
    }

    /**
     * MCPプロキシ経由の統合テスト用に、実際の MCP サーバー（SSE）を用意する。
     * 起動していなければ起動し、プロキシが接続する 127.0.0.1:{port} へ実際に到達できるまで待つ。
     * 到達できない場合はスキップせず明示的に失敗させる（サーバー起動の不具合を隠さない）。
     *
     * @return void
     */
    private function requireMcpServer(): void
    {
        $mcpServerManager = new McpServerManger();
        $config = $mcpServerManager->getServerConfig();
        if (!$mcpServerManager->isServerRunning()) {
            $mcpServerManager->startMcpServer($config);
        }
        // プロセス存在だけでなく、プロキシが叩く 127.0.0.1:{port} へ実際に接続できるまで待つ。
        // （プロセス起動直後はポート bind が間に合わず接続拒否 → 500 になることがあるため）
        $host = $config['host'] ?? '127.0.0.1';
        $port = (int)($config['port'] ?? 3000);
        $deadline = microtime(true) + 15.0;
        $reachable = false;
        while (microtime(true) < $deadline) {
            $conn = @fsockopen($host, $port, $errno, $errstr, 1);
            if ($conn) {
                fclose($conn);
                $reachable = true;
                break;
            }
            usleep(300000); // 0.3秒
        }
        $this->assertTrue(
            $reachable,
            sprintf('MCP サーバー（SSE / %s:%d）へ接続できませんでした。サーバーが起動しているか確認してください。', $host, $port)
        );
    }

    /**
     * テスト用のRSAキーペアを生成
     */
    private function generateTestKeys(string $privateKeyPath, string $publicKeyPath): void
    {
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);

        $pubKey = openssl_pkey_get_details($res);
        $publicKey = $pubKey["key"];

        file_put_contents($privateKeyPath, $privKey);
        file_put_contents($publicKeyPath, $publicKey);
    }

    /**
     * Test authorize endpoint with authenticated user
     * デフォルトクライアントの認証テスト（DCR前提とするため一旦廃止）
     * @return void
     */
//    public function testAuthorizeEndpointWithAuthenticatedUser(): void
//    {
//        $this->loginAdmin($this->getRequest());
//
//        // 認可リクエストのパラメータ
//        $params = [
//            'client_id' => 'mcp-client',
//            'client_secret' => 'mcp-secret-key',
//            'response_type' => 'code',
//            'redirect_uri' => 'http://localhost',
//            'scope' => 'mcp:read mcp:write',
//            'state' => 'test-state'
//        ];
//
//        $this->get('/baser/admin/bc-mcp/oauth2/authorize?' . http_build_query($params));
//
//        // 認証済みユーザーなので認可画面が表示される
//        $this->assertResponseOk();
//    }

    /**
     * Test authorize endpoint without authentication
     *
     * @return void
     */
    public function testAuthorizeEndpointWithoutAuthentication(): void
    {
        // 認証なしでauthorizeエンドポイントにアクセス
        $this->get('/baser/admin/bc-mcp/oauth2/authorize');

        // 認証が必要なため、リダイレクトが返される
        $this->assertResponseCode(302);
    }

    public function testIntegration(): void
    {
        $this->requireMcpServer();
        // MPCサーバーの接続ポイントにGETリクエストを送信
        $this->get('/bc-mcp');
        $this->assertResponseCode(401);

        // oauth-protected-resource にリクエストを送信
        $this->get('/.well-known/oauth-protected-resource/bc-mcp');
        $metadata = json_decode((string)$this->_response->getBody(), true);
        $this->assertTextContains('/bc-mcp', $metadata['resource']);

        // oauth-authorization-server にリクエストを送信
        $this->get('/.well-known/oauth-authorization-server/bc-mcp');
        $metadata = json_decode((string)$this->_response->getBody(), true);
        $registrationEndpoint = $metadata['registration_endpoint'];

        // クライアント登録エンドポイントにPOSTリクエストを送信
        $this->post($registrationEndpoint, [
            'client_name' => 'Test Client',
            'client_uri' => 'http://localhost',
            'redirect_uris' => ['http://localhost/callback'],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'response_types' => ['code'],
            'scope' => 'mcp:read mcp:write'
        ]);
        $metadata = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(201);
        $this->assertArrayHasKey('client_id', $metadata);

        // 認可リクエスト
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
                'client_id' => $metadata['client_id'],
                'client_secret' => $metadata['client_secret'],
                'response_type' => 'code',
                'redirect_uri' => $metadata['redirect_uris'][0]
            ]));
        $this->assertResponseCode(302);

        $this->loginAdmin($this->getRequest());
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
                'client_id' => $metadata['client_id'],
                'client_secret' => $metadata['client_secret'],
                'response_type' => 'code',
                'redirect_uri' => $metadata['redirect_uris'][0]
            ]));
        $this->assertResponseCode(200);

        // 認可承認
        $this->post('/bc-mcp/oauth2/authorize?' . http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => $metadata['client_id'],
                'client_secret' => $metadata['client_secret'],
                'response_type' => 'code',
                'redirect_uri' => $metadata['redirect_uris'][0]
            ]), ['action' => 'approve']);
        $this->assertResponseCode(302);
        $redirectUrl = $this->_response->getHeaderLine('Location');
        $this->assertStringContainsString('code=', $redirectUrl);
        // 認可コードを取得
        $queryParams = [];
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParams);
        $this->assertArrayHasKey('code', $queryParams);
        $authCode = $queryParams['code'];

        // 認可コードを使用してアクセストークンを取得
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $metadata['redirect_uris'][0],
            'client_id' => $metadata['client_id'],
            'client_secret' => $metadata['client_secret'],
            'scope' => 'read write'
        ]);
        $this->assertResponseCode(200);
        $tokenData = json_decode((string)$this->_response->getBody(), true);
        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'];

        // リフレッシュトークンが取得できていることを確認
        $this->assertArrayHasKey('refresh_token', $tokenData);
        $this->assertNotEmpty($refreshToken);

        // アクセストークンを使用してMCPサーバーのツールリストを取得
        $requestConfig = [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        // MCPプロキシ経由でtools/listを呼び出し
        $mcpRequest = [
            'jsonrpc' => '2.0',
            'id' => 'test-tools-list',
            'method' => 'tools/list'
        ];
        $this->configRequest($requestConfig);
        $this->post('/bc-mcp', json_encode($mcpRequest));
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        $toolsResponse = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($toolsResponse, 'MCP tools list response should be valid JSON');
        $this->assertArrayHasKey('result', $toolsResponse);
        $this->assertArrayHasKey('tools', $toolsResponse['result']);
        $this->assertIsArray($toolsResponse['result']['tools']);

        // ツールリストの内、ブログ記事一覧の取得ツールを実行
        $tools = $toolsResponse['result']['tools'];
        // ツールリストに getBlogPostsが含まれていることを確認
        $this->assertTrue(in_array('getBlogPosts', array_column($tools, 'name')), 'getBlogPosts tool should be available');

        // ブログ記事一覧取得ツールを実行
        $blogRequest = [
            'jsonrpc' => '2.0',
            'id' => 'test-blog-tool',
            'method' => 'tools/call',
            'params' => [
                'name' => 'getBlogPosts',
                'arguments' => []
            ]
        ];
        $this->configRequest($requestConfig);
        $this->post('/bc-mcp', json_encode($blogRequest));
        $this->assertResponseCode(200);

        $blogResponse = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($blogResponse);
        $this->assertArrayHasKey('result', $blogResponse);

        // リフレッシュトークンを使用して新しいアクセストークンを取得
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $metadata['client_id'],
            'client_secret' => $metadata['client_secret']
        ]);
        $this->assertResponseCode(200);
        $newTokenData = json_decode((string)$this->_response->getBody(), true);
        $newAccessToken = $newTokenData['access_token'];

        // 新しいアクセストークンが取得できていることを確認
        $this->assertArrayHasKey('access_token', $newTokenData);
        $this->assertNotEmpty($newAccessToken);
        $this->assertNotEquals($accessToken, $newAccessToken, 'New access token should be different from the original');

        // 新しいアクセストークンを使用してgetBlogPostツールを実行
        $newRequestConfig = [
            'headers' => [
                'Authorization' => 'Bearer ' . $newAccessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        // getBlogPostツールを実行（IDが必要な場合はダミーIDを使用）
        $blogPostRequest = [
            'jsonrpc' => '2.0',
            'id' => 'test-blog-post-tool',
            'method' => 'tools/call',
            'params' => [
                'name' => 'getBlogPost',
                'arguments' => [
                    'id' => 1 // ダミーID
                ]
            ]
        ];
        $this->configRequest($newRequestConfig);
        $this->post('/bc-mcp', json_encode($blogPostRequest));

        // レスポンスコードが200または404（データが存在しない場合）であることを確認
        $this->assertTrue(
            in_array($this->_response->getStatusCode(), [200, 404]),
            'getBlogPost should return 200 (success) or 404 (not found)'
        );

        if ($this->_response->getStatusCode() === 200) {
            $blogPostResponse = json_decode((string)$this->_response->getBody(), true);
            $this->assertNotNull($blogPostResponse);
            $this->assertArrayHasKey('result', $blogPostResponse);
        }
    }

    /**
     * PKCE (Proof Key for Code Exchange) フローの統合テスト
     * ChatGPTコネクタで使用されるPKCEフローをテスト
     *
     * @return void
     */
    public function testIntegrationWithPKCE(): void
    {
        $this->requireMcpServer();
        // Step 1: OAuth2メタデータの取得
        $this->get('/.well-known/oauth-authorization-server/bc-mcp');
        $metadata = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseOk();
        $this->assertArrayHasKey('registration_endpoint', $metadata);
        $this->assertArrayHasKey('code_challenge_methods_supported', $metadata);
        $this->assertContains('S256', $metadata['code_challenge_methods_supported']);

        // Step 2: 動的クライアント登録
        $registrationEndpoint = $metadata['registration_endpoint'];
        $this->post($registrationEndpoint, [
            'client_name' => 'ChatGPT Connector Test',
            'client_uri' => 'https://chatgpt.com',
            'redirect_uris' => ['https://chatgpt.com/connector_platform_oauth_redirect'],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'response_types' => ['code'],
            'token_endpoint_auth_method' => 'none', // PKCEではclient_secretは不要
            'scope' => 'mcp:read mcp:write'
        ]);
        $this->assertResponseCode(201);
        $clientData = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('client_id', $clientData);
        $clientId = $clientData['client_id'];
        $redirectUri = $clientData['redirect_uris'][0];

        // Step 3: PKCE パラメータの生成
        $codeVerifier = $this->generateCodeVerifier();
        $codeChallenge = $this->generateCodeChallenge($codeVerifier);
        $state = bin2hex(random_bytes(16));

        // Step 4: 認可リクエスト（PKCEパラメータ付き）
        $authParams = [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => 'mcp:read mcp:write',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256'
        ];

        // 未認証でのアクセス
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query($authParams));
        $this->assertResponseCode(302); // ログイン画面へリダイレクト

        // 管理者でログイン
        $this->loginAdmin($this->getRequest());
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query($authParams));
        $this->assertResponseOk(); // 認可画面が表示される

        // Step 5: 認可承認（PKCEパラメータが保存される）
        $this->post('/bc-mcp/oauth2/authorize?' . http_build_query($authParams), [
            'action' => 'approve',
            'scope' => 'mcp:read mcp:write'
        ]);
        $this->assertResponseCode(302);

        // リダイレクトURLから認可コードを取得
        $redirectUrl = $this->_response->getHeaderLine('Location');
        $this->assertStringContainsString('code=', $redirectUrl);
        $this->assertStringContainsString('state=' . $state, $redirectUrl);

        $queryParams = [];
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParams);
        $this->assertArrayHasKey('code', $queryParams);
        $this->assertEquals($state, $queryParams['state']);
        $authCode = $queryParams['code'];

        // Step 6: アクセストークン交換（PKCE検証）
        $tokenParams = [
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'code_verifier' => $codeVerifier // client_secretの代わりにcode_verifierを使用
        ];

        $this->post('/bc-mcp/oauth2/token', $tokenParams);
        $this->assertResponseOk();

        $tokenData = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('access_token', $tokenData);
        $this->assertArrayHasKey('token_type', $tokenData);
        $this->assertEquals('Bearer', $tokenData['token_type']);
        $accessToken = $tokenData['access_token'];

        // Step 7: 不正なcode_verifierでのテスト（失敗することを確認）
        $invalidTokenParams = [
            'grant_type' => 'authorization_code',
            'code' => $authCode, // 同じ認可コードを再利用（実際は無効化されているはず）
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'code_verifier' => 'invalid_verifier'
        ];

        $this->post('/bc-mcp/oauth2/token', $invalidTokenParams);
        $this->assertResponseError(); // 400番台のエラーが返されることを確認

        // Step 8: アクセストークンを使用してMCPサーバーにアクセス
        $requestConfig = [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        // MCPプロキシ経由でtools/listを呼び出し
        $mcpRequest = [
            'jsonrpc' => '2.0',
            'id' => 'pkce-test-tools-list',
            'method' => 'tools/list'
        ];

        $this->configRequest($requestConfig);
        $this->post('/bc-mcp', json_encode($mcpRequest));
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $toolsResponse = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($toolsResponse);
        $this->assertArrayHasKey('result', $toolsResponse);
        $this->assertArrayHasKey('tools', $toolsResponse['result']);
        $this->assertIsArray($toolsResponse['result']['tools']);

        // Step 9: ツール実行テスト
        $tools = $toolsResponse['result']['tools'];
        if (!empty($tools)) {
            $firstTool = $tools[0];
            $toolRequest = [
                'jsonrpc' => '2.0',
                'id' => 'pkce-test-tool-call',
                'method' => 'tools/call',
                'params' => [
                    'name' => $firstTool['name'],
                    'arguments' => []
                ]
            ];

            $this->configRequest($requestConfig);
            $this->post('/bc-mcp', json_encode($toolRequest));
            // ツールによってはパラメータが必要な場合があるので、200または400を許可
            $this->assertTrue(
                in_array($this->_response->getStatusCode(), [200, 400]),
                'Tool call should return 200 (success) or 400 (missing parameters)'
            );
        }
    }

    /**
     * PKCEのcode_verifierを生成
     * RFC 7636 に準拠した43-128文字のランダム文字列
     *
     * @return string
     */
    private function generateCodeVerifier(): string
    {
        $length = 43; // 最小文字数
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $verifier = '';

        for($i = 0; $i < $length; $i++) {
            $verifier .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $verifier;
    }

    /**
     * PKCEのcode_challengeを生成
     * code_verifierのSHA256ハッシュをBase64URL エンコード
     *
     * @param string $codeVerifier
     * @return string
     */
    private function generateCodeChallenge(string $codeVerifier): string
    {
        $hash = hash('sha256', $codeVerifier, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    /**
     * PKCEセキュリティテスト - 不正なcode_verifierでの失敗を確認
     *
     * @return void
     */
    public function testPKCESecurityFailure(): void
    {
        // クライアント登録
        $this->get('/.well-known/oauth-authorization-server/bc-mcp');
        $metadata = json_decode((string)$this->_response->getBody(), true);
        $registrationEndpoint = $metadata['registration_endpoint'];

        $this->post($registrationEndpoint, [
            'client_name' => 'PKCE Security Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code'],
            'response_types' => ['code'],
            'token_endpoint_auth_method' => 'none',
            'scope' => 'mcp:read'
        ]);
        $clientData = json_decode((string)$this->_response->getBody(), true);
        $clientId = $clientData['client_id'];
        $redirectUri = $clientData['redirect_uris'][0];

        // PKCE パラメータ生成
        $codeVerifier = $this->generateCodeVerifier();
        $codeChallenge = $this->generateCodeChallenge($codeVerifier);

        // 認可フロー
        $this->loginAdmin($this->getRequest());
        $authParams = [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256'
        ];

        $this->post('/bc-mcp/oauth2/authorize?' . http_build_query($authParams), [
            'action' => 'approve'
        ]);

        $redirectUrl = $this->_response->getHeaderLine('Location');
        $queryParams = [];
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParams);
        $authCode = $queryParams['code'];

        // 正しいcode_verifierでトークン交換（成功するはず）
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'code_verifier' => $codeVerifier
        ]);
        $this->assertResponseOk();
        $tokenData = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('access_token', $tokenData);

        // 新しい認可コードを取得（同じ認可コードは再利用できないため）
        $codeVerifier2 = $this->generateCodeVerifier();
        $codeChallenge2 = $this->generateCodeChallenge($codeVerifier2);
        $authParams2 = [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'code_challenge' => $codeChallenge2,
            'code_challenge_method' => 'S256'
        ];

        $this->post('/bc-mcp/oauth2/authorize?' . http_build_query($authParams2), [
            'action' => 'approve'
        ]);

        $redirectUrl2 = $this->_response->getHeaderLine('Location');
        $queryParams2 = [];
        parse_str(parse_url($redirectUrl2, PHP_URL_QUERY), $queryParams2);
        $authCode2 = $queryParams2['code'];

        // 間違ったcode_verifierでトークン交換（失敗するはず）
        $wrongVerifier = $this->generateCodeVerifier(); // 別のverifierを生成
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'authorization_code',
            'code' => $authCode2,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'code_verifier' => $wrongVerifier
        ]);

        // PKCE検証失敗でエラーが返されることを確認
        $this->assertResponseError();
        $errorResponse = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('error', $errorResponse);
        $this->assertEquals('invalid_grant', $errorResponse['error']);
    }

}
