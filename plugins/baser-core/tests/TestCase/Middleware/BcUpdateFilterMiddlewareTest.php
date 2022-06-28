<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Error\BcException;
use BaserCore\Middleware\BcUpdateFilterMiddleware;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;

/**
 * BcUpdateFilterMiddlewareTest
 */
class BcUpdateFilterMiddlewareTest extends BcTestCase
{

    /**
     * fixtures
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Middleware = new BcUpdateFilterMiddleware();
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Middleware);
    }

    /**
     * test process exception
     */
    public function test_processException()
    {
        $this->fixtureStrategy->teardownTest();
        // ソースコードとDBのバージョン違い
        $this->expectException(BcException::class);
        $this->Middleware->process($this->getRequest(), $this->Application);
    }

    /**
     * test process update
     */
    public function test_processUpdate()
    {
        // ソースコードとDBのバージョン違い
        // アップデーター
        $this->_response = $this->Middleware->process($this->getRequest('/update'), $this->Application);
        $this->assertTrue(Configure::read('BcRequest.isUpdater'));
    }

    /**
     * test process normal
     */
    public function test_proccessNormal()
    {
        // 正常リクエスト
        $this->_response = $this->Middleware->process($this->getRequest('/'), $this->Application);
        $this->assertResponseOk();
    }

}
