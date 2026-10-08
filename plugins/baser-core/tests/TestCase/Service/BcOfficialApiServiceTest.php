<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.2
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\BcOfficialApiService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * Class BcOfficialApiServiceTest
 * @property BcOfficialApiService $BcOfficialApiService
 */
class BcOfficialApiServiceTest extends BcTestCase
{

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcOfficialApiService = new BcOfficialApiService();
        Cache::delete('testRss', '_bc_update_');
        Cache::delete('testRss.failed', '_bc_update_');
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        Cache::delete('testRss', '_bc_update_');
        Cache::delete('testRss.failed', '_bc_update_');
        Configure::delete('BcLinks.testRss');
        parent::tearDown();
    }

    /**
     * 外部取得に使う HTTP クライアントにはタイムアウトが設定されている
     */
    public function testCreateClientHasTimeout()
    {
        $client = $this->execPrivateMethod($this->BcOfficialApiService, 'createClient');
        $this->assertSame(5, $client->getConfig('timeout'));
        $this->assertTrue($client->getConfig('redirect'));
    }

    /**
     * 取得に失敗した場合は空配列を返し、失敗したことを記録して次回はすぐに空配列を返す
     */
    public function testGetRssCachesFailure()
    {
        // 接続拒否される URL（即座に失敗する）
        Configure::write('BcLinks.testRss', 'http://127.0.0.1:9/index.rss');

        $this->assertSame([], $this->BcOfficialApiService->getRss('testRss'));
        $this->assertNotNull(Cache::read('testRss.failed', '_bc_update_'), '失敗が記録されていません');

        // 2 回目は到達不能な URL を解決できない URL に差し替えても、記録を見てすぐ空配列を返す
        Configure::write('BcLinks.testRss', 'http://invalid.invalid/index.rss');
        $start = microtime(true);
        $this->assertSame([], $this->BcOfficialApiService->getRss('testRss'));
        $this->assertLessThan(1.0, microtime(true) - $start, '失敗の記録があるのに再取得しています');
    }

    /**
     * 失敗の記録が古い場合は再取得を試みる
     */
    public function testGetRssRetriesAfterFailureExpired()
    {
        Configure::write('BcLinks.testRss', 'http://127.0.0.1:9/index.rss');
        Cache::write('testRss.failed', time() - 601, '_bc_update_');
        $this->assertSame([], $this->BcOfficialApiService->getRss('testRss'));
        $this->assertGreaterThan(time() - 60, Cache::read('testRss.failed', '_bc_update_'), '失敗の記録が更新されていません');
    }

}
