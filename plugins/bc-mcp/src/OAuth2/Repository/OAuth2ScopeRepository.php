<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Repository;

use BcMcp\OAuth2\Entity\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * OAuth2 Scope Repository
 */
class OAuth2ScopeRepository implements ScopeRepositoryInterface
{

    /**
     * 利用可能なスコープ
     *
     * @var array
     */
    private array $scopes;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // デフォルトのスコープ情報を設定
        $this->scopes = [
            'mcp:read' => 'データの読み取り',
            'mcp:write' => 'データの書き込み',
        ];
    }

    /**
     * スコープエンティティを取得
     *
     * @param string $identifier スコープ識別子
     * @return ScopeEntityInterface|null
     */
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        if (!isset($this->scopes[$identifier])) {
            return null;
        }

        return new Scope($identifier, $this->scopes[$identifier]);
    }

    /**
     * スコープを最終化
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param string|null $userIdentifier
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array
    {
        // クライアントが要求したスコープをそのまま返す
        return $scopes;
    }

}
