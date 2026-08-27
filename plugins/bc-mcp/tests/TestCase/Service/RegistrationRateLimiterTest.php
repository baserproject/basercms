<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Service;

use BcMcp\Service\RegistrationRateLimiter;
use Cake\Cache\Cache;
use Cake\Cache\Engine\FileEngine;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * RegistrationRateLimiter Test Case
 */
class RegistrationRateLimiterTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG)) {
            Cache::setConfig(RegistrationRateLimiter::CACHE_CONFIG, [
                'className' => FileEngine::class,
                'duration' => '+1 hours',
                'path' => CACHE . 'bc_mcp' . DS,
                'prefix' => 'bc_mcp_registration_',
            ]);
        }
        Cache::clear(RegistrationRateLimiter::CACHE_CONFIG);
        Configure::write('BcMcp.registration.maxPerHour', 3);
    }

    /**
     * 上限までは通り、超えたら止まる
     *
     * @return void
     */
    public function testIsExceededAfterReachingLimit(): void
    {
        $limiter = new RegistrationRateLimiter();
        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse($limiter->isExceeded('192.0.2.1'), $i . '回目は通ること');
            $limiter->hit('192.0.2.1');
        }
        $this->assertTrue($limiter->isExceeded('192.0.2.1'));
    }

    /**
     * IP ごとに独立して数える
     *
     * @return void
     */
    public function testCountsPerClientIp(): void
    {
        $limiter = new RegistrationRateLimiter();
        for ($i = 0; $i < 3; $i++) {
            $limiter->hit('192.0.2.1');
        }
        $this->assertTrue($limiter->isExceeded('192.0.2.1'));
        $this->assertFalse($limiter->isExceeded('192.0.2.2'));
    }
}
