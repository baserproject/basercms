<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Controller;

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
            'grant_types' => ['authorization_code', 'client_credentials'],
            'scope' => 'mcp:read mcp:write',
            'token_endpoint_auth_method' => 'client_secret_basic',
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
        $this->assertArrayHasKey('client_secret', $response);
        $this->assertArrayHasKey('registration_access_token', $response);
        $this->assertArrayHasKey('registration_client_uri', $response);
        $this->assertArrayHasKey('client_id_issued_at', $response);

        // Check provided fields
        $this->assertEquals('Test Dynamic Client', $response['client_name']);
        $this->assertEquals(['https://example.com/callback'], $response['redirect_uris']);
        $this->assertEquals(['authorization_code', 'client_credentials'], $response['grant_types']);
        $this->assertEquals('mcp:read mcp:write', $response['scope']);
        $this->assertEquals('client_secret_basic', $response['token_endpoint_auth_method']);
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
            'grant_types' => ['client_credentials'],
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
        $this->assertEquals(['client_credentials'], $response['grant_types']);
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
            'grant_types' => ['client_credentials'],
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
            'grant_types' => ['client_credentials']
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
            'grant_types' => ['client_credentials']
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

}
