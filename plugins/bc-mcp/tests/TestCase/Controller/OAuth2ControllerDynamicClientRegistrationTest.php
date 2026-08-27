<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Controller;

use BcMcp\Service\RegistrationRateLimiter;
use Cake\Cache\Cache;
use Cake\Cache\Engine\FileEngine;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * OAuth2Controller Dynamic Client Registration Test Case
 */
class OAuth2ControllerDynamicClientRegistrationTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->loadPlugins(['BcMcp']);
        parent::setUp();

        // CSRF保護を無効にする（CakePHP 5対応）
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // レート制限の枠がテスト間で持ち越されないようにする。アプリの
        // ブートストラップ前に RegistrationRateLimiter を直接使うテストもあるため、
        // 未登録の場合はここで登録しておく（RegistrationRateLimiterTest と同じ設定）
        if (!Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG)) {
            Cache::setConfig(RegistrationRateLimiter::CACHE_CONFIG, [
                'className' => FileEngine::class,
                'duration' => '+1 hours',
                'path' => CACHE . 'bc_mcp' . DS,
                'prefix' => 'bc_mcp_registration_',
            ]);
        }
        Cache::clear(RegistrationRateLimiter::CACHE_CONFIG);
    }

    /**
     * Test dynamic client registration
     *
     * @return void
     */
    public function testDynamicClientRegistration(): void
    {
        $requestData = [
            'client_name' => 'Test Dynamic Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'scope' => 'mcp:read mcp:write',
            'token_endpoint_auth_method' => 'none',
            'contacts' => ['admin@example.com'],
            'client_uri' => 'https://example.com',
            'logo_uri' => 'https://example.com/logo.png'
        ];

        // JSONデータとして送信するための設定
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // JSONエンコードしたデータを直接送信
        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));

        $this->assertResponseCode(201);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);

        // Check required RFC7591 fields
        $this->assertArrayHasKey('client_id', $response);
        $this->assertArrayNotHasKey('client_secret', $response);
        $this->assertArrayHasKey('registration_access_token', $response);
        $this->assertArrayHasKey('registration_client_uri', $response);
        $this->assertArrayHasKey('client_id_issued_at', $response);

        // Check provided fields
        $this->assertEquals('Test Dynamic Client', $response['client_name']);
        $this->assertEquals(['https://example.com/callback'], $response['redirect_uris']);
        $this->assertEquals(['authorization_code', 'refresh_token'], $response['grant_types']);
        $this->assertEquals('mcp:read mcp:write', $response['scope']);
        $this->assertEquals('none', $response['token_endpoint_auth_method']);
        $this->assertEquals(['admin@example.com'], $response['contacts']);
        $this->assertEquals('https://example.com', $response['client_uri']);
        $this->assertEquals('https://example.com/logo.png', $response['logo_uri']);
    }

    /**
     * Test client configuration retrieval
     *
     * @return void
     */
    public function testClientConfigurationRetrieval(): void
    {
        // First register a client
        $requestData = [
            'client_name' => 'Test Config Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code'],
            'scope' => 'mcp:read'
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(201);

        $registrationResponse = json_decode((string)$this->_response->getBody(), true);
        $clientId = $registrationResponse['client_id'];
        $registrationToken = $registrationResponse['registration_access_token'];

        // Then retrieve client configuration
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer ' . $registrationToken,
                'Accept' => 'application/json'
            ]
        ]);

        $this->get('/bc-mcp/oauth2/register/' . $clientId);
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('Test Config Client', $response['client_name']);
        $this->assertEquals(['https://example.com/callback'], $response['redirect_uris']);
        $this->assertEquals(['authorization_code'], $response['grant_types']);
        $this->assertEquals('mcp:read', $response['scope']);
    }

    /**
     * Test client configuration update
     *
     * @return void
     */
    public function testClientConfigurationUpdate(): void
    {
        // First register a client
        $requestData = [
            'client_name' => 'Test Update Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code'],
            'scope' => 'mcp:read'
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(201);

        $registrationResponse = json_decode((string)$this->_response->getBody(), true);
        $clientId = $registrationResponse['client_id'];
        $registrationToken = $registrationResponse['registration_access_token'];

        // Update client configuration
        $updateData = [
            'client_name' => 'Updated Client Name',
            'redirect_uris' => ['https://updated.com/callback'],
            'scope' => 'mcp:read mcp:write'
        ];

        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer ' . $registrationToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->put('/bc-mcp/oauth2/register/' . $clientId, json_encode($updateData));
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('Updated Client Name', $response['client_name']);
        $this->assertEquals(['https://updated.com/callback'], $response['redirect_uris']);
        $this->assertEquals('mcp:read mcp:write', $response['scope']);
    }

    /**
     * Test client deletion
     *
     * @return void
     */
    public function testClientDeletion(): void
    {
        // First register a client
        $requestData = [
            'client_name' => 'Test Delete Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code']
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(201);

        $registrationResponse = json_decode((string)$this->_response->getBody(), true);
        $clientId = $registrationResponse['client_id'];
        $registrationToken = $registrationResponse['registration_access_token'];

        // Delete the client
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer ' . $registrationToken,
                'Accept' => 'application/json'
            ]
        ]);

        $this->delete('/bc-mcp/oauth2/register/' . $clientId);
        $this->assertResponseCode(204); // No Content

        // Verify client is deleted by trying to retrieve it
        $this->get('/bc-mcp/oauth2/register/' . $clientId);
        $this->assertResponseCode(401); // Unauthorized (client not found)
    }

    /**
     * Test invalid client metadata
     *
     * @return void
     */
    public function testInvalidClientMetadata(): void
    {
        $requestData = [
            'client_name' => 'Invalid Client',
            'redirect_uris' => ['invalid-uri'], // Invalid URI
            'grant_types' => ['authorization_code']
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(400);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('invalid_client_metadata', $response['error']);
        $this->assertStringContainsString('Invalid redirect_uri', $response['error_description']);
    }

    /**
     * redirect_uris がトップレベルで配列でない場合は 400 になる（TypeError による 500 の退行防止）
     *
     * @return void
     */
    public function testRegisterWithNonArrayRedirectUrisReturnsBadRequest(): void
    {
        $requestData = [
            'client_name' => 'Non Array Redirect Uris Client',
            'redirect_uris' => 'not-an-array',
            'grant_types' => ['authorization_code']
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(400);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('invalid_client_metadata', $response['error']);
    }

    /**
     * 更新（PUT）でも redirect_uris がトップレベルで配列でない場合は 400 になる
     *
     * @return void
     */
    public function testUpdateWithNonArrayRedirectUrisReturnsBadRequest(): void
    {
        // First register a valid client
        $requestData = [
            'client_name' => 'Test Update Guard Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code']
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(201);

        $registrationResponse = json_decode((string)$this->_response->getBody(), true);
        $clientId = $registrationResponse['client_id'];
        $registrationToken = $registrationResponse['registration_access_token'];

        $updateData = [
            'redirect_uris' => 'not-an-array',
        ];

        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer ' . $registrationToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->put('/bc-mcp/oauth2/register/' . $clientId, json_encode($updateData));
        $this->assertResponseCode(400);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('invalid_client_metadata', $response['error']);
    }

    /**
     * Test unsupported grant type
     *
     * @return void
     */
    public function testUnsupportedGrantType(): void
    {
        $requestData = [
            'client_name' => 'Unsupported Grant Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['unsupported_grant'] // Unsupported grant type
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(400);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('invalid_client_metadata', $response['error']);
        $this->assertStringContainsString('Unsupported grant_type', $response['error_description']);
    }

    /**
     * Test invalid registration access token
     *
     * @return void
     */
    public function testInvalidRegistrationAccessToken(): void
    {
        // Register a client first
        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code']
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $registrationResponse = json_decode((string)$this->_response->getBody(), true);
        $clientId = $registrationResponse['client_id'];

        // Try to access with invalid token
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer invalid_token',
                'Accept' => 'application/json'
            ]
        ]);

        $this->get('/bc-mcp/oauth2/register/' . $clientId);
        $this->assertResponseCode(401);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('invalid_token', $response['error']);
    }

    /**
     * 上限に達していない状態では通常どおり 201 が返る
     *
     * @return void
     */
    public function testRegisterReturnsCreatedWhenUnderLimit(): void
    {
        $requestData = [
            'client_name' => 'Under Limit Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code']
        ];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(201);
    }

    /**
     * 上限を超えた登録リクエストは 429 になる
     *
     * setting.php の BcMcp.registration.maxPerHour はリクエストのたびに
     * プラグイン設定が再読込されて上書きされてしまうため、テスト内で
     * Configure::write() しても複数リクエストをまたいで維持できない。
     * そのため既定値（10件/時）を前提に、RegistrationRateLimiter を直接
     * 使って上限に達するまでカウントを進めてから検証する。
     *
     * @return void
     */
    public function testRegisterReturnsTooManyRequestsWhenLimitExceeded(): void
    {
        $clientIp = '203.0.113.10';
        $this->configRequest([
            'environment' => ['REMOTE_ADDR' => $clientIp],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // 既定の上限（10件/時）まで直接カウントを進めておく
        $rateLimiter = new RegistrationRateLimiter();
        for ($i = 0; $i < 10; $i++) {
            $rateLimiter->hit($clientIp);
        }

        $requestData = [
            'client_name' => 'Rate Limited Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code']
        ];

        // 上限に達しているため 429 になる
        $this->post('/bc-mcp/oauth2/register', json_encode($requestData));
        $this->assertResponseCode(429);
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('invalid_request', $response['error']);
    }

    /**
     * バリデーションエラーで 400 になった試行はレート制限のカウントに含まれない
     *
     * 既定の上限（10件/時）の1つ手前までカウントを進めておき、400 の試行を
     * 挟んでも正当なリクエストが通ることを確認する（400 の試行がカウントされて
     * いれば、この時点で上限に達し 429 になってしまうはず）。
     *
     * @return void
     */
    public function testInvalidRegistrationDoesNotCountTowardRateLimit(): void
    {
        $clientIp = '203.0.113.11';
        $this->configRequest([
            'environment' => ['REMOTE_ADDR' => $clientIp],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // 既定の上限（10件/時）の1つ手前までカウントを進めておく
        $rateLimiter = new RegistrationRateLimiter();
        for ($i = 0; $i < 9; $i++) {
            $rateLimiter->hit($clientIp);
        }

        // 不正なメタデータで 400 を発生させる（redirect_uris が不正な URI）
        $invalidRequestData = [
            'client_name' => 'Invalid Client',
            'redirect_uris' => ['invalid-uri'],
            'grant_types' => ['authorization_code']
        ];
        $this->post('/bc-mcp/oauth2/register', json_encode($invalidRequestData));
        $this->assertResponseCode(400);

        // 400 の試行はカウントされないため、まだ1件分の枠が残っており通る
        $validRequestData = [
            'client_name' => 'Valid Client After Invalid Attempt',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code']
        ];
        $this->configRequest([
            'environment' => ['REMOTE_ADDR' => $clientIp],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->post('/bc-mcp/oauth2/register', json_encode($validRequestData));
        $this->assertResponseCode(201);
    }

}
