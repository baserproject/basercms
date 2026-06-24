<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Service;

use BcMcp\OAuth2\Service\OAuth2ClientRegistrationService;
use BcMcp\OAuth2\Repository\OAuth2ClientRepository;
use Cake\TestSuite\TestCase;

/**
 * OAuth2ClientRegistrationService Test Case
 */
class OAuth2ClientRegistrationServiceTest extends TestCase
{
    /**
     * Test subject
     *
     * @var OAuth2ClientRegistrationService
     */
    protected OAuth2ClientRegistrationService $service;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $clientRepository = new OAuth2ClientRepository();
        $this->service = new OAuth2ClientRegistrationService($clientRepository);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->service);
        parent::tearDown();
    }

    /**
     * Test registerClient method
     *
     * @return void
     */
    public function testRegisterClient(): void
    {
        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code'],
            'scope' => 'mcp:read mcp:write',
            'token_endpoint_auth_method' => 'client_secret_basic',
            'contacts' => ['admin@example.com']
        ];

        $baseUrl = 'https://localhost';
        $client = $this->service->registerClient($requestData, $baseUrl);

        $this->assertNotNull($client);
        $this->assertEquals('Test Client', $client->getName());
        $this->assertEquals(['https://example.com/callback'], $client->getRedirectUri());
        $this->assertEquals(['authorization_code'], $client->getGrants());
        $this->assertEquals(['mcp:read', 'mcp:write'], $client->getScopes());
        $this->assertEquals('client_secret_basic', $client->getTokenEndpointAuthMethod());
        $this->assertEquals(['admin@example.com'], $client->getContacts());
        $this->assertNotNull($client->getRegistrationAccessToken());
        $this->assertNotNull($client->getRegistrationClientUri());
        $this->assertNotNull($client->getClientIdIssuedAt());
    }

    /**
     * Test registerClient with invalid redirect URI
     *
     * @return void
     */
    public function testRegisterClientWithInvalidRedirectUri(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid redirect_uri: invalid-uri');

        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['invalid-uri'],
            'grant_types' => ['authorization_code']
        ];

        $this->service->registerClient($requestData, 'https://localhost');
    }

    /**
     * Test registerClient with unsupported grant type
     *
     * @return void
     */
    public function testRegisterClientWithUnsupportedGrantType(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported grant_type: unsupported_grant');

        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['unsupported_grant']
        ];

        $this->service->registerClient($requestData, 'https://localhost');
    }

    /**
     * Test getClient method
     *
     * @return void
     */
    public function testGetClient(): void
    {
        // First register a client
        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['client_credentials']
        ];

        $client = $this->service->registerClient($requestData, 'https://localhost');
        $clientId = $client->getIdentifier();
        $registrationToken = $client->getRegistrationAccessToken();

        // Then retrieve it
        $retrievedClient = $this->service->getClient($clientId, $registrationToken);

        $this->assertNotNull($retrievedClient);
        $this->assertEquals($clientId, $retrievedClient->getIdentifier());
        $this->assertEquals('Test Client', $retrievedClient->getName());
    }

    /**
     * Test getClient with invalid token
     *
     * @return void
     */
    public function testGetClientWithInvalidToken(): void
    {
        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['client_credentials']
        ];

        $client = $this->service->registerClient($requestData, 'https://localhost');
        $clientId = $client->getIdentifier();

        // Try to retrieve with invalid token
        $retrievedClient = $this->service->getClient($clientId, 'invalid_token');
        $this->assertNull($retrievedClient);
    }

    /**
     * Test updateClient method
     *
     * @return void
     */
    public function testUpdateClient(): void
    {
        // First register a client
        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['client_credentials']
        ];

        $client = $this->service->registerClient($requestData, 'https://localhost');
        $clientId = $client->getIdentifier();
        $registrationToken = $client->getRegistrationAccessToken();

        // Update the client
        $updateData = [
            'client_name' => 'Updated Client',
            'redirect_uris' => ['https://updated.com/callback'],
            'scope' => 'mcp:read'
        ];

        $updatedClient = $this->service->updateClient($clientId, $registrationToken, $updateData);

        $this->assertNotNull($updatedClient);
        $this->assertEquals('Updated Client', $updatedClient->getName());
        $this->assertEquals(['https://updated.com/callback'], $updatedClient->getRedirectUri());
        $this->assertEquals(['mcp:read'], $updatedClient->getScopes());
    }

    /**
     * Test deleteClient method
     *
     * @return void
     */
    public function testDeleteClient(): void
    {
        // First register a client
        $requestData = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['client_credentials']
        ];

        $client = $this->service->registerClient($requestData, 'https://localhost');
        $clientId = $client->getIdentifier();
        $registrationToken = $client->getRegistrationAccessToken();

        // Delete the client
        $result = $this->service->deleteClient($clientId, $registrationToken);
        $this->assertTrue($result);

        // Verify it's deleted
        $retrievedClient = $this->service->getClient($clientId, $registrationToken);
        $this->assertNull($retrievedClient);
    }

    /**
     * Test RFC7591 compliance response
     *
     * @return void
     */
    public function testRfc7591ComplianceResponse(): void
    {
        $requestData = [
            'client_name' => 'RFC7591 Test Client',
            'redirect_uris' => ['https://example.com/callback'],
            'grant_types' => ['authorization_code', 'client_credentials'],
            'scope' => 'mcp:read mcp:write',
            'token_endpoint_auth_method' => 'client_secret_post',
            'contacts' => ['admin@example.com', 'support@example.com'],
            'client_uri' => 'https://example.com',
            'logo_uri' => 'https://example.com/logo.png',
            'tos_uri' => 'https://example.com/tos',
            'policy_uri' => 'https://example.com/policy',
            'software_id' => 'test-software-123',
            'software_version' => '1.0.0'
        ];

        $client = $this->service->registerClient($requestData, 'https://localhost');
        $response = $client->toRegistrationResponse();

        // Check required fields
        $this->assertArrayHasKey('client_id', $response);
        $this->assertArrayHasKey('client_secret', $response);
        $this->assertArrayHasKey('registration_access_token', $response);
        $this->assertArrayHasKey('registration_client_uri', $response);
        $this->assertArrayHasKey('client_id_issued_at', $response);

        // Check optional fields
        $this->assertEquals('RFC7591 Test Client', $response['client_name']);
        $this->assertEquals(['https://example.com/callback'], $response['redirect_uris']);
        $this->assertEquals(['authorization_code', 'client_credentials'], $response['grant_types']);
        $this->assertEquals('mcp:read mcp:write', $response['scope']);
        $this->assertEquals('client_secret_post', $response['token_endpoint_auth_method']);
        $this->assertEquals(['admin@example.com', 'support@example.com'], $response['contacts']);
        $this->assertEquals('https://example.com', $response['client_uri']);
        $this->assertEquals('https://example.com/logo.png', $response['logo_uri']);
        $this->assertEquals('https://example.com/tos', $response['tos_uri']);
        $this->assertEquals('https://example.com/policy', $response['policy_uri']);
        $this->assertEquals('test-software-123', $response['software_id']);
        $this->assertEquals('1.0.0', $response['software_version']);
    }
}
