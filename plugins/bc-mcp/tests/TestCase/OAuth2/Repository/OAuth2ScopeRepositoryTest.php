<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\OAuth2\Repository;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\OAuth2\Entity\Client;
use BcMcp\OAuth2\Entity\Scope;
use BcMcp\OAuth2\Repository\OAuth2ScopeRepository;
use Cake\ORM\TableRegistry;

/**
 * OAuth2ScopeRepositoryTest
 */
class OAuth2ScopeRepositoryTest extends BcTestCase
{

    private function saveClient(string $clientId, array $scopes): Client
    {
        $table = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        $table->saveOrFail($table->newEntity([
            'client_id' => $clientId,
            'name' => $clientId,
            'redirect_uris' => ['https://example.com/cb'],
            'grants' => ['authorization_code'],
            'scopes' => $scopes,
            'is_confidential' => false,
        ]));
        $client = new Client();
        $client->setIdentifier($clientId);
        return $client;
    }

    private function scopeIds(array $scopes): array
    {
        return array_map(fn($scope) => $scope->getIdentifier(), $scopes);
    }

    /**
     * test finalizeScopes - クライアント登録時のスコープの範囲に限定されること
     */
    public function testFinalizeScopesLimitsToRegisteredScopes()
    {
        $repository = new OAuth2ScopeRepository();
        $requested = [new Scope('mcp:read', ''), new Scope('mcp:write', '')];

        // read のみで登録したクライアントは write を取得できない
        $client = $this->saveClient('read_only', ['mcp:read']);
        $this->assertEquals(['mcp:read'], $this->scopeIds($repository->finalizeScopes($requested, 'authorization_code', $client)));

        // 両方登録していれば両方
        $client = $this->saveClient('read_write', ['mcp:read', 'mcp:write']);
        $this->assertEquals(['mcp:read', 'mcp:write'], $this->scopeIds($repository->finalizeScopes($requested, 'authorization_code', $client)));

        // スコープを要求しない場合は登録スコープの範囲で全てを与える
        $client = $this->saveClient('read_only_default', ['mcp:read']);
        $this->assertEquals(['mcp:read'], $this->scopeIds($repository->finalizeScopes([], 'authorization_code', $client)));
        $client = $this->saveClient('no_scope_default', []);
        $this->assertEquals(['mcp:read', 'mcp:write'], $this->scopeIds($repository->finalizeScopes([], 'authorization_code', $client)));

        // 登録スコープが無い、または既知でない場合は制限しない（従来互換）
        $client = $this->saveClient('no_scope', []);
        $this->assertEquals(['mcp:read', 'mcp:write'], $this->scopeIds($repository->finalizeScopes($requested, 'authorization_code', $client)));
        $client = $this->saveClient('legacy_scope', ['read', 'write']);
        $this->assertEquals(['mcp:read', 'mcp:write'], $this->scopeIds($repository->finalizeScopes($requested, 'authorization_code', $client)));
    }

}
