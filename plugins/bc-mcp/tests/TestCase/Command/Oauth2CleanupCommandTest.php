<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

/**
 * Oauth2CleanupCommand Test Case
 */
class Oauth2CleanupCommandTest extends BcTestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * 一度も使われていない古いクライアントを削除する
     *
     * @return void
     */
    public function testExecuteRemovesUnusedClients(): void
    {
        $clientsTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        $tokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AccessTokens');

        // 古くて未使用のクライアント（削除される）
        $clientsTable->saveOrFail($clientsTable->newEntity([
            'client_id' => 'client_unused_old',
            'name' => 'Unused Old',
            'redirect_uris' => ['https://example.com/callback'],
            'grants' => ['authorization_code'],
            'scopes' => ['mcp:read'],
            'is_confidential' => false,
            'created' => new DateTime('-40 days'),
            'modified' => new DateTime('-40 days'),
        ]));

        // 古いが使用実績のあるクライアント（残る）
        $clientsTable->saveOrFail($clientsTable->newEntity([
            'client_id' => 'client_used_old',
            'name' => 'Used Old',
            'redirect_uris' => ['https://example.com/callback'],
            'grants' => ['authorization_code'],
            'scopes' => ['mcp:read'],
            'is_confidential' => false,
            'created' => new DateTime('-40 days'),
            'modified' => new DateTime('-40 days'),
        ]));
        $tokensTable->saveOrFail($tokensTable->newEntity([
            'token_id' => 'token_for_used_old',
            'client_id' => 'client_used_old',
            'user_id' => 1,
            'scopes' => 'mcp:read',
            'expires_at' => new DateTime('+1 hour'),
            'revoked' => false,
        ]));

        // 新しくて未使用のクライアント（残る）
        $clientsTable->saveOrFail($clientsTable->newEntity([
            'client_id' => 'client_unused_new',
            'name' => 'Unused New',
            'redirect_uris' => ['https://example.com/callback'],
            'grants' => ['authorization_code'],
            'scopes' => ['mcp:read'],
            'is_confidential' => false,
        ]));

        $clientsTable->updateAll(
            ['created' => new DateTime('-40 days')],
            ['client_id IN' => ['client_unused_old', 'client_used_old']]
        );

        $this->exec('bc_mcp.oauth2_cleanup');
        $this->assertExitSuccess();

        $this->assertNull($clientsTable->findByClientId('client_unused_old'));
        $this->assertNotNull($clientsTable->findByClientId('client_used_old'));
        $this->assertNotNull($clientsTable->findByClientId('client_unused_new'));
    }
}
