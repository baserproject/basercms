<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Service;

use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use BcMcp\OAuth2\Service\OAuth2Service;

/**
 * OAuth2Service Test Case
 */
class OAuth2ServiceTest extends TestCase
{
    /**
     * @var OAuth2Service
     */
    private OAuth2Service $oauth2Service;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // OAuth2設定をセットアップ
        Configure::write('BcMcp.OAuth2.clients', [
            'test-client' => [
                'name' => 'Test Client',
                'secret' => null,
                'redirect_uris' => ['http://localhost'],
                'grants' => ['authorization_code', 'refresh_token'],
                'scopes' => ['read', 'write']
            ]
        ]);

        Configure::write('BcMcp.OAuth2.scopes', [
            'read' => 'データの読み取り',
            'write' => 'データの書き込み'
        ]);

        $this->oauth2Service = new OAuth2Service();
    }

    /**
     * Test OAuth2 authorization server creation
     *
     * @return void
     */
    public function testAuthorizationServerCreation(): void
    {
        $server = $this->oauth2Service->getAuthorizationServer();
        $this->assertInstanceOf(\League\OAuth2\Server\AuthorizationServer::class, $server);
    }

    /**
     * Test OAuth2 resource server creation
     *
     * @return void
     */
    public function testResourceServerCreation(): void
    {
        $server = $this->oauth2Service->getResourceServer();
        $this->assertInstanceOf(\League\OAuth2\Server\ResourceServer::class, $server);
    }

    /**
     * Test access token validation with invalid token
     *
     * @return void
     */
    public function testValidateAccessTokenWithInvalidToken(): void
    {
        $result = $this->oauth2Service->validateAccessToken('invalid-token');
        $this->assertNull($result);
    }

    /**
     * 鍵ペア生成時、秘密鍵ファイルが所有者のみ読める権限（0600）で作成される
     *
     * GHSA-8qvr-v52m-fj2h: 既定の umask のまま file_put_contents すると 0644 となり、
     * 同一ホストの他アカウントが署名鍵を読み取れてしまう。
     *
     * @return void
     */
    public function testGenerateKeyPairWritesPrivateKeyWithRestrictedPermissions(): void
    {
        $privateKeyPath = CONFIG . 'oauth2_private.key';
        $publicKeyPath = CONFIG . 'oauth2_public.key';
        $backup = $this->backupKeyFiles($privateKeyPath, $publicKeyPath);
        $oldUmask = umask(0022);
        try {
            @unlink($privateKeyPath);
            @unlink($publicKeyPath);
            clearstatcache();

            new OAuth2Service();

            $this->assertFileExists($privateKeyPath);
            $this->assertFileExists($publicKeyPath);
            $this->assertSame('600', decoct(fileperms($privateKeyPath) & 0777));
            // 生成後の umask は元に戻っている
            $this->assertSame(0022, umask());
        } finally {
            umask($oldUmask);
            $this->restoreKeyFiles($backup, $privateKeyPath, $publicKeyPath);
        }
    }

    /**
     * 既存の秘密鍵が 0644 のまま残っている場合、読み込み時に 0600 へ是正される
     *
     * @return void
     */
    public function testGetPrivateKeyRestrictsPermissionsOfExistingKey(): void
    {
        $privateKeyPath = CONFIG . 'oauth2_private.key';
        $publicKeyPath = CONFIG . 'oauth2_public.key';
        $backup = $this->backupKeyFiles($privateKeyPath, $publicKeyPath);
        try {
            $service = new OAuth2Service();
            chmod($privateKeyPath, 0644);
            clearstatcache();
            $this->assertSame('644', decoct(fileperms($privateKeyPath) & 0777));

            $service->getAuthorizationServer();

            clearstatcache();
            $this->assertSame('600', decoct(fileperms($privateKeyPath) & 0777));
        } finally {
            $this->restoreKeyFiles($backup, $privateKeyPath, $publicKeyPath);
        }
    }

    /**
     * 鍵ファイルを退避する
     *
     * @param string $privateKeyPath
     * @param string $publicKeyPath
     * @return array{private: string|null, public: string|null, privatePerms: int|null}
     */
    private function backupKeyFiles(string $privateKeyPath, string $publicKeyPath): array
    {
        return [
            'private' => is_file($privateKeyPath)? file_get_contents($privateKeyPath) : null,
            'public' => is_file($publicKeyPath)? file_get_contents($publicKeyPath) : null,
            'privatePerms' => is_file($privateKeyPath)? fileperms($privateKeyPath) & 0777 : null,
        ];
    }

    /**
     * 退避した鍵ファイルを復元する
     *
     * @param array{private: string|null, public: string|null, privatePerms: int|null} $backup
     * @param string $privateKeyPath
     * @param string $publicKeyPath
     * @return void
     */
    private function restoreKeyFiles(array $backup, string $privateKeyPath, string $publicKeyPath): void
    {
        if ($backup['private'] === null) {
            @unlink($privateKeyPath);
        } else {
            file_put_contents($privateKeyPath, $backup['private']);
            if ($backup['privatePerms'] !== null) {
                chmod($privateKeyPath, $backup['privatePerms']);
            }
        }
        if ($backup['public'] === null) {
            @unlink($publicKeyPath);
        } else {
            file_put_contents($publicKeyPath, $backup['public']);
        }
        clearstatcache();
    }

    /**
     * 暗号化キーが未設定なら例外を投げる
     *
     * @return void
     */
    public function testConstructThrowsWhenEncryptionKeyIsMissing(): void
    {
        $original = env('OAUTH2_ENC_KEY');
        putenv('OAUTH2_ENC_KEY');
        unset($_ENV['OAUTH2_ENC_KEY'], $_SERVER['OAUTH2_ENC_KEY']);
        try {
            $this->expectException(\BcMcp\OAuth2\Exception\OAuth2ConfigurationException::class);
            new \BcMcp\OAuth2\Service\OAuth2Service();
        } finally {
            if ($original !== null) {
                putenv('OAUTH2_ENC_KEY=' . $original);
                $_ENV['OAUTH2_ENC_KEY'] = $original;
            }
        }
    }
}
