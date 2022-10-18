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

use BaserCore\Service\Front\ContentFoldersFrontService;
use BaserCore\TestSuite\BcTestCase;

/**
 * ContentFoldersFrontServiceTest
 *
 * @property ContentFoldersFrontService $ContentFoldersFrontService
 */
class ContentFoldersFrontServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFoldersFrontService = new ContentFoldersFrontService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFoldersFrontService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setupPreviewForView
     */
    public function test_setupPreviewForView()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
