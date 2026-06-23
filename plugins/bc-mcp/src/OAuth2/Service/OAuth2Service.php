<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Service;

use BcMcp\OAuth2\Repository\OAuth2AccessTokenRepository;
use BcMcp\OAuth2\Repository\OAuth2ClientRepository;
use BcMcp\OAuth2\Repository\OAuth2ScopeRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\CryptKey;

/**
 * OAuth2 Service (moved under OAuth2\Service)
 */
class OAuth2Service
{

    /**
     * Authorization Server インスタンス
     * @var AuthorizationServer|null
     */
    private ?AuthorizationServer $authorizationServer = null;

    /**
     * Resource Server インスタンス
     * @var ResourceServer|null
     */
    private ?ResourceServer $resourceServer = null;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        if (!file_exists(CONFIG . 'oauth2_public.key')) {
            $this->generateKeyPair();
        }
    }

    /**
     * Get Authorization Server
     * @return AuthorizationServer
     */
    public function getAuthorizationServer(): AuthorizationServer
    {
        if ($this->authorizationServer === null) {
            $this->authorizationServer = $this->createAuthorizationServer();
        }
        return $this->authorizationServer;
    }

    /**
     * Get Resource Server
     * @return ResourceServer
     */
    public function getResourceServer(): ResourceServer
    {
        if ($this->resourceServer === null) {
            $this->resourceServer = $this->createResourceServer();
        }
        return $this->resourceServer;
    }

    /**
     * Create Authorization Server
     * @return AuthorizationServer
     * @throws \Exception
     */
    private function createAuthorizationServer(): AuthorizationServer
    {
        $clientRepository = new OAuth2ClientRepository();
        $accessTokenRepository = OAuth2AccessTokenRepository::getInstance();
        $scopeRepository = new OAuth2ScopeRepository();

        $authCodeRepository = new \BcMcp\OAuth2\Repository\OAuth2AuthCodeRepository();
        $refreshTokenRepository = new \BcMcp\OAuth2\Repository\OAuth2RefreshTokenRepository();
        $userRepository = new \BcMcp\OAuth2\Repository\OAuth2UserRepository();

        $privateKey = $this->getPrivateKey();
        $encryptionKey = $this->getEncryptionKey();

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        $clientCredentialsGrant = new ClientCredentialsGrant();
        $server->enableGrantType(
            $clientCredentialsGrant,
            new \DateInterval('PT1H')
        );

        $authCodeGrant = new \BcMcp\OAuth2\Grant\AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval('PT10M')
        );
        $authCodeGrant->setRefreshTokenTTL(new \DateInterval('P1M'));
        $server->enableGrantType(
            $authCodeGrant,
            new \DateInterval('PT1H')
        );

        $refreshTokenGrant = new \League\OAuth2\Server\Grant\RefreshTokenGrant(
            $refreshTokenRepository
        );
        $refreshTokenGrant->setRefreshTokenTTL(new \DateInterval('P1M'));
        $server->enableGrantType(
            $refreshTokenGrant,
            new \DateInterval('PT1H')
        );

        return $server;
    }

    /**
     * Create Resource Server
     * @return ResourceServer
     * @throws \Exception
     */
    private function createResourceServer(): ResourceServer
    {
        $accessTokenRepository = OAuth2AccessTokenRepository::getInstance();
        $publicKey = $this->getPublicKey();
        return new ResourceServer(
            $accessTokenRepository,
            $publicKey
        );
    }

    /**
     * Get Private Key
     * @return CryptKey
     */
    private function getPrivateKey(): CryptKey
    {
        $keyPath = CONFIG . 'oauth2_private.key';
        if (!file_exists($keyPath)) {
            $this->generateKeyPair();
        }
        return new CryptKey($keyPath, null, false);
    }

    /**
     * Get Public Key
     * @return CryptKey
     */
    private function getPublicKey(): CryptKey
    {
        $keyPath = CONFIG . 'oauth2_public.key';
        if (!file_exists($keyPath)) {
            $this->generateKeyPair();
        }
        return new CryptKey($keyPath, null, false);
    }

    /**
     * Get Encryption Key
     * @return string
     */
    private function getEncryptionKey(): string
    {
        return env('OAUTH2_ENC_KEY', 'j6eyb4oPtNL0R8i9uU8PlQJ2WY1f8yRk5AVXb7OJd3s');
    }

    /**
     * Generate RSA Key Pair
     * @return void
     * @throws \Exception
     */
    private function generateKeyPair(): void
    {
        $privateKeyPath = CONFIG . 'oauth2_private.key';
        $publicKeyPath = CONFIG . 'oauth2_public.key';

        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);

        $pubKey = openssl_pkey_get_details($res);
        $publicKey = $pubKey['key'];

        file_put_contents($privateKeyPath, $privKey);
        file_put_contents($publicKeyPath, $publicKey);
    }

    /**
     * Validate Access Token
     * @param string $token
     * @return array|null
     */
    public function validateAccessToken(string $token): ?array
    {
        try {
            $resourceServer = $this->getResourceServer();
            $siteUrl = env('SITE_URL', 'https://localhost');
            $request = new \Nyholm\Psr7\ServerRequest(
                'GET',
                $siteUrl,
                ['Authorization' => 'Bearer ' . $token]
            );
            $request = $resourceServer->validateAuthenticatedRequest($request);
            return [
                'client_id' => $request->getAttribute('oauth_client_id'),
                'user_id' => $request->getAttribute('oauth_user_id'),
                'scope' => $request->getAttribute('oauth_scopes', [])
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Store Authorization Code
     * @param array $data
     * @return void
     */
    public function storeAuthorizationCode(array $data): void
    {
        $authCodeRepository = new \BcMcp\OAuth2\Repository\OAuth2AuthCodeRepository();
        $authCodeRepository->storeAuthorizationCode($data);
    }

}
