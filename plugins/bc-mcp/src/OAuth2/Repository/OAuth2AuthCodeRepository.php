<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Repository;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use BcMcp\OAuth2\Entity\AuthCode as OAuth2AuthCode;
use Cake\ORM\TableRegistry;
use Cake\I18n\DateTime;

/**
 * OAuth2 Authorization Code Repository
 *
 * 認可コードの管理を行う（データベース永続化）
 */
class OAuth2AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * OAuth2AuthCodes Table
     *
     * @var \BcMcp\Model\Table\Oauth2AuthCodesTable
     */
    private $authCodesTable;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->authCodesTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AuthCodes');
    }

    /**
     * 新しい認可コードエンティティを作成
     *
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new OAuth2AuthCode();
    }

    /**
     * 認可コードを永続化
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     * @return void
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        // データベースに保存
        $entityData = [
            'code' => $authCodeEntity->getIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'scopes' => implode(' ', array_map(fn($scope) => $scope->getIdentifier(), $authCodeEntity->getScopes())),
            'expires_at' => DateTime::createFromInterface($authCodeEntity->getExpiryDateTime()),
            'redirect_uri' => $authCodeEntity->getRedirectUri(),
            'revoked' => false
        ];

        $authCode = $this->authCodesTable->newEntity($entityData);

        if (!$this->authCodesTable->save($authCode)) {
            throw new \RuntimeException('Failed to save authorization code to database');
        }
    }

    /**
     * 認可コードを無効化
     *
     * @param string $codeId
     * @return void
     */
    public function revokeAuthCode($codeId): void
    {
        // データベースで無効化
        $authCode = $this->authCodesTable->find()
            ->where(['code' => $codeId])
            ->first();

        if ($authCode) {
            $authCode->revoked = true;
            $this->authCodesTable->save($authCode);
        }
    }

    /**
     * 認可コードが無効化されているかチェック
     *
     * @param string $codeId
     * @return bool
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        // データベースから確認
        $authCode = $this->authCodesTable->find()
            ->where(['code' => $codeId])
            ->first();

        if ($authCode) {
            // 期限切れもチェック
            $now = new DateTime();
            if ($authCode->expires_at < $now) {
                return true;
            }
            return $authCode->revoked;
        }

        return true; // 見つからない場合は無効扱い
    }

    /**
     * 認可コードを保存（OAuth2Controller から呼び出される）
     *
     * @param array $data
     * @return void
     */
    public function storeAuthorizationCode(array $data): void
    {
        // データベースに保存
        $authCode = $this->authCodesTable->newEntity([
            'code' => $data['code'],
            'client_id' => $data['client_id'],
            'user_id' => $data['user_id'],
            'scopes' => is_array($data['scope'] ?? [])?
                implode(' ', $data['scope']) :
                ($data['scope'] ?? ''),
            'expires_at' => DateTime::createFromTimestamp($data['expires_at']),
            'redirect_uri' => $data['redirect_uri'],
            'revoked' => false
        ]);

        if (!$this->authCodesTable->save($authCode)) {
            throw new \RuntimeException('Failed to save authorization code to database');
        }
    }

    /**
     * 認可コードを取得
     *
     * @param string $code
     * @return array|null
     */
    public function getAuthorizationCode(string $code): ?array
    {
        // データベースから取得
        $authCode = $this->authCodesTable->find()
            ->where(['code' => $code])
            ->first();

        if ($authCode) {
            return [
                'code' => $authCode->code,
                'client_id' => $authCode->client_id,
                'user_id' => $authCode->user_id,
                'scope' => $authCode->scopes,
                'scopes' => $authCode->getScopesArray(),
                'expires_at' => $authCode->expires_at->getTimestamp(),
                'redirect_uri' => $authCode->redirect_uri,
                'revoked' => $authCode->revoked
            ];
        }

        return null;
    }

    /**
     * 期限切れの認可コードをクリーンアップ
     *
     * @return int 削除された件数
     */
    public function cleanExpiredCodes(): int
    {
        return $this->authCodesTable->cleanExpiredCodes();
    }
}
