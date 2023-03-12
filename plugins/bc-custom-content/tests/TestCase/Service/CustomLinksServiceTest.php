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
use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomLinksServiceTest
 */
class CustomLinksServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomLinksService
     */
    public $CustomLinksService;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcCustomContent.Factory/CustomFields',
        'plugin.BcCustomContent.Factory/CustomLinks',
    ];

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomLinksService = $this->getService(CustomLinksServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomLinksService);
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
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $result = $this->CustomLinksService->getList(1);
        $this->assertEquals('求人分類', $result[1]);
        $this->assertEquals('この仕事の特徴', $result[2]);
    }

}
