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

namespace BcCustomContent\Test\TestCase\Service\Front;


use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\Front\CustomContentFrontService;
use BcCustomContent\Service\Front\CustomContentFrontServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsFrontServiceTest
 */
class CustomContentsFrontServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentFrontService
     */
    public $CustomContentFrontService;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcCustomContent.Factory/CustomContents',
        'plugin.BaserCore.Factory/Contents',
    ];

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentFrontService = $this->getService(CustomContentFrontServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentFrontService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->CustomContentFrontService->entriesService));
        $this->assertTrue(isset($this->CustomContentFrontService->contentsService));
    }

    /**
     * test getCustomContent
     */
    public function test_getCustomContent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getCustomEntries
     */
    public function test_getCustomEntries()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
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

    /**
     * test getViewTemplate
     */
    public function test_getViewTemplate()
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

    /**
     * test setupPreviewForIndex
     */
    public function test_setupPreviewForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
