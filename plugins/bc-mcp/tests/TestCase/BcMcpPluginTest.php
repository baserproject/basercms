<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Service\RegistrationRateLimiter;
use Cake\Cache\Cache;
use Cake\Cache\Engine\FileEngine;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BcMcpPlugin Test Case
 *
 * 本番の登録経路（BcMcpPlugin::bootstrap()）がレート制限用のキャッシュ設定を
 * 実際に登録することを検証する。
 *
 * OAuth2ControllerDynamicClientRegistrationTest 等は setUp() で自ら
 * Cache::setConfig() してしまうため、bootstrap() の配線が壊れていても
 * テストが緑になってしまう。ここでは Cache::drop() で一旦設定を外した上で
 * アプリケーションへ実際にリクエストを送り、Application::bootstrap() 経由の
 * BcMcpPlugin::bootstrap() だけでキャッシュ設定が復元されることを確認する。
 */
class BcMcpPluginTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * BcMcpPlugin::bootstrap() でレート制限用のキャッシュ設定が登録されること
     *
     * @return void
     */
    public function testBootstrapRegistersRegistrationRateLimiterCacheConfig(): void
    {
        // テスト側での事前登録に頼らず、本番の配線だけで登録されることを見るため、
        // まず登録を外しておく
        Cache::drop(RegistrationRateLimiter::CACHE_CONFIG);
        $this->assertNull(
            Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG),
            '前提として、リクエスト前はキャッシュ設定が登録されていないこと'
        );

        // アプリケーションへ実際にリクエストを送ることで、
        // Application::bootstrap() 経由の BcMcpPlugin::bootstrap() を実行させる
        $this->get('/.well-known/oauth-protected-resource/bc-mcp');

        $config = Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG);
        $this->assertNotNull(
            $config,
            'BcMcpPlugin::bootstrap() の配線が壊れていると、ここでキャッシュ設定が登録されずレート制限が全く効かなくなる'
        );
        $this->assertSame(FileEngine::class, $config['className']);
    }
}
