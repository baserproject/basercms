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

namespace BaserCore\Test\TestCase\Service\Front;

use BaserCore\Service\ContentsService;
use BaserCore\Service\Front\BcFrontContentsService;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;

/**
 * BcFrontContentsServiceTest
 *
 * @property BcFrontContentsService $BcFrontContentsService
 */
class BcFrontContentsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents'
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFrontContentsService = new BcFrontContentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcFrontContentsService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForFront
     */
    public function test_getViewVarsForFront()
    {
        ContentFactory::make(['id' => 100, 'title' => 'test title', 'description' => 'description test', 'lft'=>1, 'rght'=>2])->persist();
        $contentService = new ContentsService();
        $content = $contentService->get(100);
        $rs = $this->BcFrontContentsService->getViewVarsForFront($content, false);
        $this->assertEquals('description test',$rs['description']);
        $this->assertEquals('test title',$rs['title']);
        $this->assertEquals('test title',$rs['crumbs'][0]['name']);
    }

    /**
     * test getCrumbs
     */
    public function test_getCrumbs()
    {
        ContentFactory::make(
            [
                'id' => 101,
                'title' => 'test title 1',
                'description' => 'description test 1',
                'lft' => 1,
                'rght' => 4,
                'url' => '/test1'
            ])->persist();
        ContentFactory::make(
            [
                'id' => 102,
                'title' => 'test title 2',
                'description' => 'description test 2',
                'lft' => 2,
                'rght' => 3,
                'url' => '/test2'
            ])->persist();
        $result = $this->execPrivateMethod($this->BcFrontContentsService, 'getCrumbs', [101, false]);
        $this->assertCount(1, $result);
        $this->assertEquals('test title 1', $result[0]['name']);
        $this->assertEquals('/test1', $result[0]['url']);

        $result = $this->execPrivateMethod($this->BcFrontContentsService, 'getCrumbs', [102, false]);
        $this->assertCount(3, $result);
        $this->assertEquals('test title 1', $result[0]['name']);
        $this->assertEquals('/test1', $result[0]['url']);
        $this->assertEquals('トップページ', $result[1]['name']);
        $this->assertEquals('test title 2', $result[2]['name']);
    }
}
