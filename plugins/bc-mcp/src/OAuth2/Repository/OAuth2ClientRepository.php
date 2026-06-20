<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Repository;

use Cake\Datasource\RepositoryInterface;
use BcMcp\OAuth2\Entity\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Cake\ORM\TableRegistry;

/**
 * OAuth2 Client Repository
 */
class OAuth2ClientRepository implements ClientRepositoryInterface
{

    /**
     * Oauth2ClientsTable インスタンス
     *
     * @var \BcMcp\Model\Table\Oauth2ClientsTable
     */
    private RepositoryInterface $clientsTable;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->clientsTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');

        // 初期化時にデフォルトクライアントが存在しない場合のみ追加
        // Dynamic Client Registration を有効にするためコメントアウト
//        $this->ensureDefaultClientsExist();
    }

    /**
     * デフォルトクライアントが存在することを確認し、なければ作成
     */
//    private function ensureDefaultClientsExist(): void
//    {
//        $defaultClient = $this->clientsTable->findByClientId('mcp-client');
//        if (!$defaultClient) {
//            // JSON型マッピングにより配列で渡せば自動的にJSONとして保存される
//            $clientData = [
//                'client_id' => 'mcp-client',
//                'client_secret' => 'mcp-secret-key',
//                'name' => 'MCP Server Client',
//                'grants' => ['client_credentials'],
//                'scopes' => ['mcp:read', 'mcp:write'],
//                'is_confidential' => true,
//                'redirect_uris' => ['http://localhost'],
//            ];
//
//            $client = $this->clientsTable->newEntity($clientData);
//            $this->clientsTable->save($client);
//        }
//    }

    /**
     * クライアントエンティティを取得
     *
     * ClientRepositoryInterface::getClientEntity($clientIdentifier) に準拠。
     * ここではエンティティ取得のみを行い、認証やグラントの検証は validateClient() 側で行う。
     *
     * @param string $clientIdentifier クライアントID
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $clientData = $this->clientsTable->findByClientId($clientIdentifier);
        if (!$clientData) {
            return null;
        }
        return $this->createClientEntity($clientData);
    }

    /**
     * クライアント認証
     *
     * @param string $clientIdentifier クライアントID
     * @param string|null $clientSecret クライアント秘密キー
     * @param string|null $grantType グラントタイプ
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $clientData = $this->clientsTable->findByClientId($clientIdentifier);

        if (!$clientData) {
            return false;
        }

        // グラントタイプの検証
        if ($grantType !== null && !in_array($grantType, $clientData->grants)) {
            return false;
        }

        // 機密クライアントの場合、シークレットキーを検証
        if ($clientData->is_confidential) {
            return !empty($clientSecret) && $clientSecret === $clientData->client_secret;
        }

        // パブリッククライアントの場合は、シークレットが空であることを確認
        return empty($clientSecret);
    }

    /**
     * 新しいクライアントを登録（Dynamic Client Registration用）
     *
     * @param array $clientData クライアントデータ
     * @return string 登録されたクライアントID
     */
    public function registerClient(array $clientData): string
    {
        $client = $this->clientsTable->newEntity($clientData);
        $savedClient = $this->clientsTable->saveOrFail($client);

        return $savedClient->client_id;
    }

    /**
     * クライアント情報を更新（Dynamic Client Registration用）
     *
     * @param string $clientId クライアントID
     * @param array $updateData 更新データ
     * @return bool 更新成功
     */
    public function updateClient(string $clientId, array $updateData): bool
    {
        $client = $this->clientsTable->findByClientId($clientId);

        if (!$client) {
            return false;
        }

        $client = $this->clientsTable->patchEntity($client, $updateData);
        return (bool)$this->clientsTable->save($client);
    }

    /**
     * クライアントを削除（Dynamic Client Registration用）
     *
     * @param string $clientId クライアントID
     * @return bool 削除成功
     */
    public function deleteClient(string $clientId): bool
    {
        $client = $this->clientsTable->findByClientId($clientId);

        if (!$client) {
            return false;
        }

        return (bool)$this->clientsTable->delete($client);
    }

    /**
     * クライアント情報を取得（Dynamic Client Registration用）
     *
     * @param string $clientId クライアントID
     * @return array|null クライアント情報
     */
    public function getClientInfo(string $clientId): ?array
    {
        $client = $this->clientsTable->findByClientId($clientId);

        if (!$client) {
            return null;
        }

        return [
            'client_id' => $client->client_id,
            'client_name' => $client->name,
            'redirect_uris' => $client->redirect_uris,
            'grant_types' => $client->grants,
            'scope' => implode(' ', $client->scopes),
            'client_id_issued_at' => $client->created? $client->created->getTimestamp() : null,
        ];
    }

    /**
     * OAuth2Clientエンティティを作成
     *
     * @param \BcMcp\Model\Entity\Oauth2Client $clientData
     * @return ClientEntityInterface
     */
    private function createClientEntity(\BcMcp\Model\Entity\Oauth2Client $clientData): ClientEntityInterface
    {
        $client = new Client();
        $client->setIdentifier($clientData->client_id);
        $client->setName($clientData->name);
        $client->setRedirectUri($clientData->redirect_uris);
        $client->setIsConfidential($clientData->is_confidential);

        return $client;
    }

}
