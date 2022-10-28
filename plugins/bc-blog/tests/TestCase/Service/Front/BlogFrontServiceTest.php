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

namespace BcBlog\Test\TestCase\Service\Front;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\Front\BlogFrontService;

/**
 * BlogFrontServiceTest
 * @property BlogFrontService $BlogFrontService
 */
class BlogFrontServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BlogFrontService = new BlogFrontService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogFrontService);
        parent::tearDown();
    }


    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setupPreviewForIndex
     */
    public function test_setupPreviewForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getIndexTemplate
     */
    public function test_getIndexTemplate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
