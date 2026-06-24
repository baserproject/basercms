<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Service;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use BcMcp\OAuth2\Service\OAuth2Service;

/**
 * OAuth2Service Test Case
 */
class OAuth2ServiceTest extends TestCase
{
    /**
     * @var OAuth2Service
     */
    private OAuth2Service $oauth2Service;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // OAuth2設定をセットアップ
        Configure::write('BcMcp.OAuth2.clients', [
            'test-client' => [
                'name' => 'Test Client',
                'secret' => null,
                'redirect_uris' => ['http://localhost'],
                'grants' => ['client_credentials'],
                'scopes' => ['read', 'write']
            ]
        ]);

        Configure::write('BcMcp.OAuth2.scopes', [
            'read' => 'データの読み取り',
            'write' => 'データの書き込み'
        ]);

        $this->oauth2Service = new OAuth2Service();
    }

    /**
     * Test OAuth2 authorization server creation
     *
     * @return void
     */
    public function testAuthorizationServerCreation(): void
    {
        $server = $this->oauth2Service->getAuthorizationServer();
        $this->assertInstanceOf(\League\OAuth2\Server\AuthorizationServer::class, $server);
    }

    /**
     * Test OAuth2 resource server creation
     *
     * @return void
     */
    public function testResourceServerCreation(): void
    {
        $server = $this->oauth2Service->getResourceServer();
        $this->assertInstanceOf(\League\OAuth2\Server\ResourceServer::class, $server);
    }

    /**
     * Test access token validation with invalid token
     *
     * @return void
     */
    public function testValidateAccessTokenWithInvalidToken(): void
    {
        $result = $this->oauth2Service->validateAccessToken('invalid-token');
        $this->assertNull($result);
    }
}
