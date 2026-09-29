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
        // スコープを要求しないクライアントには従来通り全てのスコープを与える
        // （スコープの検証を追加したことで、スコープを要求しない既存クライアントが
        // 全てのツールを実行できなくなるのを防ぐ）
        if (!$scopes) {
            foreach(array_keys($this->scopes) as $identifier) {
                $scopes[] = new Scope($identifier, $this->scopes[$identifier]);
            }
        }

        // クライアント登録時に許可されたスコープの範囲に限定する。
        // 要求スコープをそのまま返すと、read のみで登録したクライアントが write を取得できてしまう
        $client = \Cake\ORM\TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients')
            ->findByClientId((string)$clientEntity->getIdentifier());
        $registered = ($client && is_array($client->scopes))? $client->scopes : [];
        // 既知のスコープ識別子のみを制限の対象にする（旧形式の登録値は無視する）
        $allowed = array_values(array_intersect($registered, array_keys($this->scopes)));
        if (!$allowed) {
            // 登録スコープが無いクライアントは制限しない（従来互換）
            return $scopes;
        }
        return array_values(array_filter($scopes, function(ScopeEntityInterface $scope) use ($allowed) {
            return in_array($scope->getIdentifier(), $allowed, true);
        }));
    }

}
