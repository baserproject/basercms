<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Repository;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use BcMcp\OAuth2\Entity\RefreshToken as OAuth2RefreshToken;
use Cake\ORM\TableRegistry;
use Cake\I18n\DateTime;

/**
 * OAuth2 Refresh Token Repository
 *
 * リフレッシュトークンの管理を行う（データベース永続化）
 */
class OAuth2RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

    /**
     * OAuth2RefreshTokens Table
     *
     * @var \BcMcp\Model\Table\Oauth2RefreshTokensTable
     */
    private $refreshTokensTable;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->refreshTokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2RefreshTokens');
    }

    /**
     * 新しいリフレッシュトークンエンティティを作成
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new OAuth2RefreshToken();
    }

    /**
     * リフレッシュトークンを永続化
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @return void
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        // データベースに保存
        $refreshToken = $this->refreshTokensTable->newEntity([
            'token_id' => $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'expires_at' => DateTime::createFromInterface($refreshTokenEntity->getExpiryDateTime()),
            'revoked' => false
        ]);

        if (!$this->refreshTokensTable->save($refreshToken)) {
            throw new \RuntimeException('Failed to save refresh token to database');
        }
    }

    /**
     * リフレッシュトークンを無効化
     *
     * @param string $tokenId
     * @return void
     */
    public function revokeRefreshToken($tokenId): void
    {
        // データベースで無効化
        $refreshToken = $this->refreshTokensTable->find()
            ->where(['token_id' => $tokenId])
            ->first();

        if ($refreshToken) {
            $refreshToken->revoked = true;
            $this->refreshTokensTable->save($refreshToken);
        }
    }

    /**
     * リフレッシュトークンが無効化されているかチェック
     *
     * @param string $tokenId
     * @return bool
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        // データベースから確認
        $refreshToken = $this->refreshTokensTable->find()
            ->where(['token_id' => $tokenId])
            ->first();

        if ($refreshToken) {
            // 期限切れもチェック
            $now = new DateTime();
            if ($refreshToken->expires_at < $now) {
                return true;
            }
            return $refreshToken->revoked;
        }

        return true; // 見つからない場合は無効扱い
    }

    /**
     * 期限切れのリフレッシュトークンをクリーンアップ
     *
     * @return int 削除された件数
     */
    public function cleanExpiredTokens(): int
    {
        return $this->refreshTokensTable->cleanExpiredTokens();
    }

}
