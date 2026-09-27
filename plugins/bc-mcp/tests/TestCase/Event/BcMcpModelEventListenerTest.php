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

namespace BcMcp\Test\TestCase\Event;

use ArrayObject;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Event\BcMcpModelEventListener;
use BcMcp\OAuth2\Repository\OAuth2UserRepository;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcMcpModelEventListenerTest
 */
class BcMcpModelEventListenerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * テスト用のトークンを作成する
     */
    private function createTokens(string $userId, string $suffix): void
    {
        $accessTokens = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AccessTokens');
        $refreshTokens = TableRegistry::getTableLocator()->get('BcMcp.Oauth2RefreshTokens');
        $accessTokens->saveOrFail($accessTokens->newEntity([
            'token_id' => 'access_' . $suffix,
            'client_id' => 'client_' . $suffix,
            'user_id' => $userId,
            'scopes' => json_encode(['mcp:read']),
            'revoked' => false,
            'expires_at' => new \DateTime('+1 hour'),
        ]));
        $refreshTokens->saveOrFail($refreshTokens->newEntity([
            'token_id' => 'refresh_' . $suffix,
            'access_token_id' => 'access_' . $suffix,
            'revoked' => false,
            'expires_at' => new \DateTime('+1 day'),
        ]));
    }

    private function isRevoked(string $table, string $tokenId): bool
    {
        return (bool)TableRegistry::getTableLocator()->get($table)->find()->where(['token_id' => $tokenId])->firstOrFail()->revoked;
    }

    /**
     * test isActiveUser
     */
    public function testIsActiveUser()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        UserFactory::make(['id' => 2, 'name' => 'disabled', 'email' => 'disabled@example.com', 'status' => false])->persist();
        $this->assertTrue(OAuth2UserRepository::isActiveUser(1));
        $this->assertTrue(OAuth2UserRepository::isActiveUser('1'));
        $this->assertFalse(OAuth2UserRepository::isActiveUser(2));
        $this->assertFalse(OAuth2UserRepository::isActiveUser(999));
        $this->assertFalse(OAuth2UserRepository::isActiveUser(null));
        $this->assertFalse(OAuth2UserRepository::isActiveUser('abc'));
    }

    /**
     * test revokeTokensByUserId - 指定ユーザーのアクセストークン・リフレッシュトークンのみ失効すること
     */
    public function testRevokeTokensByUserId()
    {
        $this->createTokens('1', 'u1');
        $this->createTokens('2', 'u2');
        OAuth2UserRepository::revokeTokensByUserId(2);
        $this->assertFalse($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u1'));
        $this->assertFalse($this->isRevoked('BcMcp.Oauth2RefreshTokens', 'refresh_u1'));
        $this->assertTrue($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u2'));
        $this->assertTrue($this->isRevoked('BcMcp.Oauth2RefreshTokens', 'refresh_u2'));
    }

    /**
     * test usersAfterSave / usersAfterDelete - ユーザーの無効化・削除でトークンが失効すること
     */
    public function testRevokeOnUserDisabledOrDeleted()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        UserFactory::make(['id' => 2, 'name' => 'operator', 'email' => 'operator@example.com', 'status' => true])->persist();
        UserFactory::make(['id' => 3, 'name' => 'operator3', 'email' => 'operator3@example.com', 'status' => true])->persist();
        $this->createTokens('2', 'u2');
        $this->createTokens('3', 'u3');
        $users = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $listener = new BcMcpModelEventListener();

        // 有効なまま保存しても失効しない
        $user = $users->get(2);
        $listener->baserCoreUsersAfterSave(new Event('Model.afterSave'), $user, new ArrayObject());
        $this->assertFalse($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u2'));

        // 無効化で失効する
        $user->status = false;
        $listener->baserCoreUsersAfterSave(new Event('Model.afterSave'), $user, new ArrayObject());
        $this->assertTrue($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u2'));
        $this->assertTrue($this->isRevoked('BcMcp.Oauth2RefreshTokens', 'refresh_u2'));
        $this->assertFalse($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u3'));

        // 削除で失効する
        $listener->baserCoreUsersAfterDelete(new Event('Model.afterDelete'), $users->get(3), new ArrayObject());
        $this->assertTrue($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u3'));
        $this->assertTrue($this->isRevoked('BcMcp.Oauth2RefreshTokens', 'refresh_u3'));
    }

    /**
     * test UsersTable の保存で実際にリスナーが動作すること
     */
    public function testListenerIsRegisteredOnUsersTable()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        UserFactory::make(['id' => 2, 'name' => 'operator', 'email' => 'operator@example.com', 'status' => true])->persist();
        $this->createTokens('2', 'u2');
        $users = TableRegistry::getTableLocator()->get('BaserCore.Users');
        // テスト環境ではモデルイベントの転送（BcModelEventDispatcher）が登録されていないため明示的に登録する
        $dispatcher = new \BaserCore\Event\BcModelEventDispatcher();
        \Cake\Event\EventManager::instance()->on($dispatcher);
        if (!\Cake\Event\EventManager::instance()->listeners('Model.BaserCore.Users.afterSave')) {
            \BaserCore\Utility\BcEvent::registerPluginEvent('BcMcp');
        }
        try {
            $user = $users->get(2);
            $user->status = false;
            $users->saveOrFail($user);
            $this->assertTrue($this->isRevoked('BcMcp.Oauth2AccessTokens', 'access_u2'));
        } finally {
            \Cake\Event\EventManager::instance()->off($dispatcher);
        }
    }

}
