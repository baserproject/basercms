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
        $rs = $this->BcFrontContentsService->getViewVarsForFront($content);
        $this->assertEquals('description test',$rs['description']);
        $this->assertEquals('test title',$rs['title']);
        $this->assertEquals('test title',$rs['crumbs'][0]['name']);
    }

    /**
     * test getCrumbs
     */
    public function test_getCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
