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

namespace BcCustomContent\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsServiceTest
 */
class CustomContentsServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentsService
     */
    public $CustomContentsService;

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
        $this->CustomContentsService = $this->getService(CustomContentsServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentsService);
        parent::tearDown();
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test get
     */
    public function test_get()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getListOrders
     */
    public function test_getListOrders()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getTemplates
     */
    public function test_getTemplates()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test unsetTable
     */
    public function test_unsetTable()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
