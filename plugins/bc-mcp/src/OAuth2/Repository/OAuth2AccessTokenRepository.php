<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Repository;

use BcMcp\OAuth2\Entity\AccessToken as OAuth2AccessToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use Cake\ORM\TableRegistry;
use Cake\I18n\DateTime;

/**
 * OAuth2 Access Token Repository
 *
 * アクセストークンの管理を行う（データベース永続化）
 */
class OAuth2AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * シングルトンインスタンス
     *
     * @var OAuth2AccessTokenRepository|null
     */
    private static ?OAuth2AccessTokenRepository $instance = null;

    /**
     * OAuth2AccessTokens Table
     *
     * @var \BcMcp\Model\Table\Oauth2AccessTokensTable
     */
    private $accessTokensTable;

    /**
     * シングルトンインスタンスを取得
     *
     * @return OAuth2AccessTokenRepository
     */
    public static function getInstance(): OAuth2AccessTokenRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->accessTokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AccessTokens');
    }

    /**
     * 新しいアクセストークンを取得
     *
     * @param ClientEntityInterface $clientEntity
     * @param array $scopes
     * @param string|int|null $userIdentifier
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new OAuth2AccessToken();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);

        foreach($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * アクセストークンを永続化
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $identifier = $accessTokenEntity->getIdentifier();

        // 重複チェック
        $existingToken = $this->accessTokensTable->find()
            ->where(['token_id' => $identifier])
            ->first();

        if ($existingToken) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
        $scopes = $accessTokenEntity->getScopes();
        $scopeArray = [];
        foreach($scopes as $scope) {
            $scopeArray[] = $scope->getIdentifier();
        }
        // データベースに保存
        $accessToken = $this->accessTokensTable->newEntity([
            'token_id' => $identifier,
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'user_id' => $accessTokenEntity->getUserIdentifier(),
            'scopes' => implode(' ', $scopeArray),
            'expires_at' => DateTime::createFromInterface($accessTokenEntity->getExpiryDateTime()),
            'revoked' => false
        ]);

        if (!$this->accessTokensTable->save($accessToken)) {
            throw new \RuntimeException('Failed to save access token to database');
        }
    }

    /**
     * アクセストークンを取り消し
     *
     * @param string $tokenId
     * @return void
     */
    public function revokeAccessToken($tokenId): void
    {
        // データベースで無効化
        $accessToken = $this->accessTokensTable->find()
            ->where(['token_id' => $tokenId])
            ->first();

        if ($accessToken) {
            $accessToken->revoked = true;
            $this->accessTokensTable->save($accessToken);
        }
    }

    /**
     * アクセストークンが取り消されているかチェック
     *
     * @param string $tokenId
     * @return bool
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        // データベースから確認
        $accessToken = $this->accessTokensTable->find()
            ->where(['token_id' => $tokenId])
            ->first();

        if (!$accessToken) {
            return true; // 見つからない場合は無効扱い
        }

        // 期限切れもチェック
        $now = new DateTime();
        if ($accessToken->expires_at < $now) {
            return true;
        }

        return $accessToken->revoked;
    }

    /**
     * アクセストークンのデータを取得（検証用）
     *
     * @param string $tokenId
     * @return array|null
     */
    public function getAccessTokenData(string $tokenId): ?array
    {
        // データベースから取得
        $accessToken = $this->accessTokensTable->find()
            ->where(['token_id' => $tokenId])
            ->first();

        if (!$accessToken) {
            return null;
        }

        if ($accessToken->revoked) {
            return null;
        }

        // 期限切れチェック
        $now = new DateTime();
        if ($accessToken->expires_at < $now) {
            return null;
        }

        return [
            'identifier' => $accessToken->token_id,
            'client_id' => $accessToken->client_id,
            'user_id' => $accessToken->user_id,
            'scopes' => explode(' ', $accessToken->scopes),
            'expires_at' => $accessToken->expires_at,
            'revoked' => $accessToken->revoked
        ];
    }

    /**
     * 期限切れのアクセストークンをクリーンアップ
     *
     * @return int 削除された件数
     */
    public function cleanExpiredTokens(): int
    {
        return $this->accessTokensTable->cleanExpiredTokens();
    }

}
