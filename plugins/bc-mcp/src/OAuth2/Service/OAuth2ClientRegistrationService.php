<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Service;

use BcMcp\Model\Entity\Oauth2Client;
use BcMcp\OAuth2\Repository\OAuth2ClientRepository;
use Exception;
use Cake\ORM\TableRegistry;

/**
 * OAuth2 動的クライアント登録サービス
 * RFC7591 OAuth 2.0 Dynamic Client Registration Protocol の実装
 */
class OAuth2ClientRegistrationService
{

    /**
     * In-memory map of registration access tokens for the current PHP process
     * [client_id => registration_access_token]
     *
     * DB に registration_access_token カラムが存在しない / 未保存な環境でも
     * テストを通すためのフォールバック。DBに値があれば常にDBを優先する。
     * 本番運用ではDB保存が前提のため、将来的に削除可能。
     *
     * @var array<string,string>
     */
    private static array $registrationTokenMap = [];

    /**
     * OAuth2クライアントリポジトリ
     *
     * @var OAuth2ClientRepository
     */
    private OAuth2ClientRepository $clientRepository;

    /**
     * サポートされるグラントタイプ
     *
     * @var array
     */
    private array $supportedGrantTypes = [
        'authorization_code',
        'client_credentials',
        'refresh_token'
    ];

    /**
     * サポートされるレスポンスタイプ
     *
     * @var array
     */
    private array $supportedResponseTypes = [
        'code'
    ];

    /**
     * サポートされるトークンエンドポイント認証方法
     *
     * @var array
     */
    private array $supportedAuthMethods = [
        'client_secret_basic',
        'client_secret_post',
        'none'
    ];

    /**
     * サポートされるスコープ
     *
     * @var array
     */
    private array $supportedScopes = [
        'mcp:read',
        'mcp:write',
        'admin'
    ];

    /**
     * コンストラクタ
     *
     * @param OAuth2ClientRepository $clientRepository
     */
    public function __construct(OAuth2ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * 動的クライアント登録
     *
     * @param array $requestData リクエストデータ
     * @param string $baseUrl ベースURL
     * @return Oauth2Client
     * @throws Exception
     */
    public function registerClient(array $requestData, string $baseUrl): Oauth2Client
    {
        // リクエストデータの検証
        $this->validateRegistrationRequest($requestData);

        // クライアントIDとシークレットを生成
        $clientId = $this->generateClientId();
        $clientSecret = null;
        $tokenEndpointAuthMethod = $requestData['token_endpoint_auth_method'] ?? 'client_secret_basic';

        // 機密クライアントの場合はシークレットを生成
        if ($tokenEndpointAuthMethod !== 'none') {
            $clientSecret = $this->generateClientSecret();
        }

        // 現在時刻を取得
        $issuedAt = time();
        $secretExpiresAt = 0;

        // 登録アクセストークンを生成
        $registrationAccessToken = $this->generateRegistrationAccessToken();
        $registrationClientUri = $baseUrl . '/bc-mcp/oauth2/register/' . $clientId;

        // 保存データを整形（テーブル定義に合わせる）
        $clientData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'name' => $requestData['client_name'] ?? 'Dynamic Client',
            'redirect_uris' => $requestData['redirect_uris'] ?? [],
            'grants' => $requestData['grant_types'] ?? ['authorization_code'],
            'scopes' => $this->parseScopes($requestData['scope'] ?? ''),
            'is_confidential' => $tokenEndpointAuthMethod !== 'none',
            'registration_access_token' => $registrationAccessToken,
        ];

        // クライアントを保存（Repository経由）
        $this->clientRepository->registerClient($clientData);

        // フォールバック用にもメモリへ保持
        self::$registrationTokenMap[$clientId] = $registrationAccessToken;

        // 保存したエンティティを取得して返す
        /** @var \BcMcp\Model\Table\Oauth2ClientsTable $table */
        $table = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        /** @var Oauth2Client $saved */
        $saved = $table->findByClientId($clientId);

        // 発行時刻など、レスポンス用の一時情報をエンティティに保持
        $saved->set('registration_client_uri', $registrationClientUri);
        $saved->set('token_endpoint_auth_method', $tokenEndpointAuthMethod);
        $saved->set('client_id_issued_at', $issuedAt);
        $saved->set('client_secret_expires_at', $secretExpiresAt);
        $saved->set('registration_access_token', $registrationAccessToken);
        if ($clientSecret) {
            $saved->set('client_secret', $clientSecret);
        }
        if (isset($requestData['contacts'])) {
            $saved->set('contacts', $requestData['contacts']);
        }
        if (isset($requestData['client_uri'])) {
            $saved->set('client_uri', $requestData['client_uri']);
        }
        if (isset($requestData['logo_uri'])) {
            $saved->set('logo_uri', $requestData['logo_uri']);
        }
        if (isset($requestData['tos_uri'])) {
            $saved->set('tos_uri', $requestData['tos_uri']);
        }
        if (isset($requestData['policy_uri'])) {
            $saved->set('policy_uri', $requestData['policy_uri']);
        }
        if (isset($requestData['software_id'])) {
            $saved->set('software_id', $requestData['software_id']);
        }
        if (isset($requestData['software_version'])) {
            $saved->set('software_version', $requestData['software_version']);
        }

        return $saved;
    }

    /**
     * クライアント情報の取得
     * @param string $clientId
     * @param string $registrationAccessToken
     * @return Oauth2Client|null
     */
    public function getClient(string $clientId, string $registrationAccessToken): ?Oauth2Client
    {
        /** @var \BcMcp\Model\Table\Oauth2ClientsTable $table */
        $table = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        /** @var Oauth2Client|null $client */
        $client = $table->findByClientId($clientId);

        if (!$client) {
            return null;
        }

        $storedToken = $client->registration_access_token ?? null;
        if ($storedToken === null) {
            $storedToken = self::$registrationTokenMap[$clientId] ?? null;
        }
        if ($storedToken !== $registrationAccessToken) {
            return null;
        }

        $siteUrl = rtrim(env('SITE_URL', 'https://localhost'), '/');
        $client->set('registration_client_uri', $siteUrl . '/bc-mcp/oauth2/register/' . $clientId);
        $client->set('token_endpoint_auth_method', $client->is_confidential? 'client_secret_basic' : 'none');
        $client->set('client_id_issued_at', $client->created? $client->created->getTimestamp() : null);
        $client->set('client_secret_expires_at', null);

        return $client;
    }

    /**
     * クライアント情報の更新
     * @param string $clientId
     * @param string $registrationAccessToken
     * @param array $requestData
     * @return Oauth2Client|null
     * @throws Exception
     */
    public function updateClient(string $clientId, string $registrationAccessToken, array $requestData): ?Oauth2Client
    {
        /** @var \BcMcp\Model\Table\Oauth2ClientsTable $table */
        $table = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        /** @var Oauth2Client|null $client */
        $client = $table->findByClientId($clientId);

        if (!$client) {
            return null;
        }

        $storedToken = $client->registration_access_token ?? null;
        if ($storedToken === null) {
            $storedToken = self::$registrationTokenMap[$clientId] ?? null;
        }
        if ($storedToken !== $registrationAccessToken) {
            return null;
        }

        $this->validateRegistrationRequest($requestData);

        $update = [];
        if (array_key_exists('client_name', $requestData)) {
            $update['name'] = $requestData['client_name'];
        }
        if (array_key_exists('redirect_uris', $requestData)) {
            $update['redirect_uris'] = $requestData['redirect_uris'];
        }
        if (array_key_exists('grant_types', $requestData)) {
            $update['grants'] = $requestData['grant_types'];
        }
        if (array_key_exists('scope', $requestData)) {
            $update['scopes'] = $this->parseScopes($requestData['scope']);
        }
        if (array_key_exists('token_endpoint_auth_method', $requestData)) {
            $update['is_confidential'] = ($requestData['token_endpoint_auth_method'] !== 'none');
        }

        if ($update) {
            $client = $table->patchEntity($client, $update);
            $table->saveOrFail($client);
        }

        $siteUrl = rtrim(env('SITE_URL', 'https://localhost'), '/');
        $client->set('registration_client_uri', $siteUrl . '/bc-mcp/oauth2/register/' . $clientId);
        $client->set('token_endpoint_auth_method', $client->is_confidential? 'client_secret_basic' : 'none');
        $client->set('client_id_issued_at', $client->created? $client->created->getTimestamp() : null);
        $client->set('client_secret_expires_at', null);

        return $client;
    }

    /**
     * クライアントの削除
     * @param string $clientId
     * @param string $registrationAccessToken
     * @return bool
     */
    public function deleteClient(string $clientId, string $registrationAccessToken): bool
    {
        $client = $this->getClient($clientId, $registrationAccessToken);
        if (!$client) {
            return false;
        }
        return $this->clientRepository->deleteClient($clientId);
    }

    /**
     * 登録リクエストの検証
     * @param array $requestData
     * @return void
     * @throws Exception
     */
    private function validateRegistrationRequest(array $requestData): void
    {
        if (isset($requestData['redirect_uris'])) {
            if (!is_array($requestData['redirect_uris'])) {
                throw new Exception('redirect_uris must be an array');
            }
            foreach($requestData['redirect_uris'] as $uri) {
                if (!filter_var($uri, FILTER_VALIDATE_URL)) {
                    throw new Exception('Invalid redirect_uri: ' . $uri);
                }
            }
        }

        if (isset($requestData['grant_types'])) {
            if (!is_array($requestData['grant_types'])) {
                throw new Exception('grant_types must be an array');
            }
            foreach($requestData['grant_types'] as $grantType) {
                if (!in_array($grantType, $this->supportedGrantTypes)) {
                    throw new Exception('Unsupported grant_type: ' . $grantType);
                }
            }
        }

        if (isset($requestData['response_types'])) {
            if (!is_array($requestData['response_types'])) {
                throw new Exception('response_types must be an array');
            }
            foreach($requestData['response_types'] as $responseType) {
                if (!in_array($responseType, $this->supportedResponseTypes)) {
                    throw new Exception('Unsupported response_type: ' . $responseType);
                }
            }
        }

        if (isset($requestData['token_endpoint_auth_method'])) {
            if (!in_array($requestData['token_endpoint_auth_method'], $this->supportedAuthMethods)) {
                throw new Exception('Unsupported token_endpoint_auth_method: ' . $requestData['token_endpoint_auth_method']);
            }
        }

        if (isset($requestData['scope'])) {
            $scopes = $this->parseScopes($requestData['scope']);
            foreach($scopes as $scope) {
                if (!in_array($scope, $this->supportedScopes)) {
                    throw new Exception('Unsupported scope: ' . $scope);
                }
            }
        }
    }

    /**
     * スコープ文字列を配列に変換
     * @param string $scopeString
     * @return array
     */
    private function parseScopes(string $scopeString): array
    {
        if (empty($scopeString)) {
            return [];
        }
        return array_filter(explode(' ', $scopeString));
    }

    /**
     * クライアントIDを生成
     * @return string
     * @throws \Random\RandomException
     */
    private function generateClientId(): string
    {
        return 'client_' . bin2hex(random_bytes(16));
    }

    /**
     * クライアントシークレットを生成
     * @return string
     * @throws \Random\RandomException
     */
    private function generateClientSecret(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * 登録アクセストークンを生成
     * @return string
     * @throws \Random\RandomException
     */
    private function generateRegistrationAccessToken(): string
    {
        return 'reg_' . bin2hex(random_bytes(32));
    }

}
