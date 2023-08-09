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
use BcCustomContent\Service\Admin\CustomContentsAdminService;
use BcCustomContent\Service\Admin\CustomContentsAdminServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsAdminServiceTest
 */
class CustomContentsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentsAdminService
     */
    public $CustomContentsAdminService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsAdminService = $this->getService(CustomContentsAdminServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentsAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //テストメソッドを呼ぶ
        $rs = $this->CustomContentsAdminService->getViewVarsForEdit($this->CustomContentsAdminService->get(1));
        $this->assertEquals(1, $rs['entity']->id);
        $this->assertArrayHasKey('customTables', $rs);
        $this->assertArrayHasKey('editorEnterBr', $rs);
    }
}
