<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Controller;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcMcp\Test\Factory\Oauth2ClientFactory;

/**
 * OAuth2Controller Test Case
 * 認証不要なOAuth2エンドポイントのテスト
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
        $this->loadPlugins(['BcMcp']);
        parent::setUp();

        // OAuth2設定をセットアップ
        Configure::write('BcMcp.OAuth2.clients', [
            'mcp-client' => [
                'name' => 'MCP Server Client',
                'secret' => 'mcp-secret-key',
                'redirect_uris' => ['http://localhost'],
                'grants' => ['authorization_code', 'refresh_token'],
                'scopes' => ['mcp:read', 'mcp:write']
            ]
        ]);

        Configure::write('BcMcp.OAuth2.scopes', [
            'mcp:read' => 'データの読み取り',
            'mcp:write' => 'データの書き込み'
        ]);

        Configure::write('OAuth2.accessTokenTTL', 'PT1H');

        // テスト用のOAuth2キーペアが存在することを確認
        $privateKeyPath = CONFIG . 'oauth2_private.key';
        $publicKeyPath = CONFIG . 'oauth2_public.key';

        if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath)) {
            $this->generateTestKeys($privateKeyPath, $publicKeyPath);
        }
    }

    /**
     * 直近の GET で発行された consent_id を取得する
     *
     * Server::run() 後の Session::read() はデータを消してしまうため、
     * $_SESSION を直接参照する（CakePHP 本体の SessionEquals/SessionHasKey
     * 制約と同様の作法）。
     *
     * @return string
     */
    private function lastConsentId(): string
    {
        $pending = (array)Hash::get($_SESSION, 'BcMcp.authRequests', []);
        return (string)array_key_last($pending);
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
     * Test token endpoint with valid client credentials (no auth required)
     *
     * @return void
     */
    public function testTokenEndpointWithValidCredentials(): void
    {
        // ファクトリの既定はパブリッククライアント（is_confidential = false）
        Oauth2ClientFactory::make()->persist();
        $this->loadFixtureScenario(InitAppScenario::class);

        $codeVerifier = 'bc-mcp-test-code-verifier-0123456789012345678901234567';
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $this->loginAdmin($this->getRequest());
        // 同意画面を開いて認可リクエストをセッションへ保持させる
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
                'client_id' => 'mcp-client',
                'response_type' => 'code',
                'redirect_uri' => 'http://localhost',
                'scope' => 'mcp:read mcp:write',
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
            ]));
        $this->assertResponseOk();

        $consentId = $this->lastConsentId();
        $this->session($_SESSION);
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve', 'consent_id' => $consentId]);

        $redirectUrl = $this->_response->getHeaderLine('Location');
        $queryParams = [];
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParams);
        $authCode = $queryParams['code'];

        // 認証なしでtokenエンドポイントをテスト
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'mcp-client',
            'redirect_uri' => 'http://localhost',
            'code_verifier' => $codeVerifier,
            'code' => $authCode
        ]);

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('token_type', $response);
        $this->assertArrayHasKey('expires_in', $response);
        $this->assertEquals('Bearer', $response['token_type']);
    }

    /**
     * Test authorization server metadata endpoint (no auth required)
     *
     * @return void
     */
    public function testAuthorizationServerMetadata(): void
    {
        $this->get('/.well-known/oauth-authorization-server/bc-mcp');

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('issuer', $response);
        $this->assertArrayHasKey('token_endpoint', $response);
        $this->assertArrayHasKey('authorization_endpoint', $response);
    }

    /**
     * Test protected resource metadata endpoint (no auth required)
     *
     * @return void
     */
    public function testProtectedResourceMetadata(): void
    {
        $this->get('/.well-known/oauth-protected-resource/bc-mcp');

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('resource', $response);
        $this->assertArrayHasKey('authorization_servers', $response);
    }

    /**
     * Test client registration endpoint (no auth required)
     *
     * @return void
     */
    public function testClientRegistration(): void
    {
        $this->post('/bc-mcp/oauth2/register', [
            'client_name' => 'Test Client',
            'client_uri' => 'http://localhost',
            'redirect_uris' => ['http://localhost/callback'],
            'grant_types' => ['authorization_code'],
            'response_types' => ['code'],
            'token_endpoint_auth_method' => 'none',
            'scope' => 'mcp:read mcp:write'
        ]);

        $this->assertResponseCode(201);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('client_id', $response);
        $this->assertArrayNotHasKey('client_secret', $response);
    }

    /**
     * JWKSエンドポイントのテスト
     */
    public function testJwks(): void
    {
        $this->get('/bc-mcp/oauth2/jwks');
        $this->assertResponseOk();
        $this->assertContentType('application/json');
        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('keys', $body);
        $this->assertNotEmpty($body['keys']);
        $key = $body['keys'][0];
        $this->assertEquals('RSA', $key['kty']);
        $this->assertEquals('RS256', $key['alg']);
        $this->assertEquals('sig', $key['use']);
        $this->assertArrayHasKey('n', $key);
        $this->assertArrayHasKey('e', $key);
    }

    /**
     * Test verify endpoint with valid token
     *
     * @return void
     */
    public function testVerifyWithValidToken(): void
    {
        // ファクトリの既定はパブリッククライアント（is_confidential = false）
        Oauth2ClientFactory::make()->persist();
        $this->loadFixtureScenario(InitAppScenario::class);

        $codeVerifier = 'bc-mcp-test-code-verifier-0123456789012345678901234567';
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $this->loginAdmin($this->getRequest());
        // 同意画面を開いて認可リクエストをセッションへ保持させる
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
                'client_id' => 'mcp-client',
                'response_type' => 'code',
                'redirect_uri' => 'http://localhost',
                'scope' => 'mcp:read mcp:write',
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
            ]));
        $this->assertResponseOk();

        $consentId = $this->lastConsentId();
        $this->session($_SESSION);
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve', 'consent_id' => $consentId]);

        $redirectUrl = $this->_response->getHeaderLine('Location');
        $queryParams = [];
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParams);
        $authCode = $queryParams['code'];

        // まず有効なトークンを取得
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'mcp-client',
            'redirect_uri' => 'http://localhost',
            'code_verifier' => $codeVerifier,
            'code' => $authCode
        ]);

        $this->assertResponseOk();
        $tokenResponse = json_decode((string)$this->_response->getBody(), true);
        $accessToken = $tokenResponse['access_token'];

        // 取得したトークンでverifyエンドポイントをテスト
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $accessToken]
        ]);
        $this->get('/bc-mcp/oauth2/verify');

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('valid', $response);
        $this->assertTrue($response['valid']);
        $this->assertArrayHasKey('client_id', $response);

        // client_idが期待される形式かどうかをチェック（URLまたは元のclient_id）
        $this->assertNotEmpty($response['client_id']);

        $this->assertArrayHasKey('scope', $response);
        $this->assertStringContainsString('mcp:read', $response['scope']);
        $this->assertStringContainsString('mcp:write', $response['scope']);
    }

    /**
     * Test verify endpoint with missing token
     *
     * @return void
     */
    public function testVerifyWithMissingToken(): void
    {
        $this->get('/bc-mcp/oauth2/verify');

        $this->assertResponseCode(401);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('invalid_token', $response['error']);
        $this->assertArrayHasKey('error_description', $response);
        $this->assertEquals('The access token is missing or invalid.', $response['error_description']);
    }

    /**
     * Test verify endpoint with invalid token format
     *
     * @return void
     */
    public function testVerifyWithInvalidTokenFormat(): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'InvalidFormat token123']
        ]);
        $this->get('/bc-mcp/oauth2/verify');

        $this->assertResponseCode(401);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('invalid_token', $response['error']);
        $this->assertArrayHasKey('error_description', $response);
        $this->assertEquals('The access token is missing or invalid.', $response['error_description']);
    }

    /**
     * Test verify endpoint with invalid token
     *
     * @return void
     */
    public function testVerifyWithInvalidToken(): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token_string']
        ]);
        $this->get('/bc-mcp/oauth2/verify');

        $this->assertResponseCode(401);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('invalid_token', $response['error']);
        $this->assertArrayHasKey('error_description', $response);
        $this->assertEquals('The access token is invalid or expired.', $response['error_description']);
    }

    /**
     * Test verify endpoint with empty Authorization header
     *
     * @return void
     */
    public function testVerifyWithEmptyAuthorizationHeader(): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => '']
        ]);
        $this->get('/bc-mcp/oauth2/verify');

        $this->assertResponseCode(401);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('invalid_token', $response['error']);
        $this->assertArrayHasKey('error_description', $response);
        $this->assertEquals('The access token is missing or invalid.', $response['error_description']);
    }

    /**
     * Test verify endpoint with Bearer but no token
     *
     * @return void
     */
    public function testVerifyWithBearerButNoToken(): void
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ']
        ]);
        $this->get('/bc-mcp/oauth2/verify');

        $this->assertResponseCode(401);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('invalid_token', $response['error']);
        $this->assertArrayHasKey('error_description', $response);
        $this->assertEquals('The access token is invalid or expired.', $response['error_description']);
    }

}
