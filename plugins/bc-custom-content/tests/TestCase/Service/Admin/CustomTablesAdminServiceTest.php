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

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\Admin\CustomTablesAdminService;
use BcCustomContent\Service\Admin\CustomTablesAdminServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomTablesAdminServiceTest
 */
class CustomTablesAdminServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomTablesAdminService
     */
    public $CustomTablesAdminService;


    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomTablesAdminService = $this->getService(CustomTablesAdminServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomTablesAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //対象メソッドをコール
        $rs = $this->CustomTablesAdminService->getViewVarsForEdit($customTable->get(1));

        //戻る値を確認
        $this->assertArrayHasKey('fields', $rs);
        $this->assertArrayHasKey('customLinks', $rs);
        $this->assertArrayHasKey('flatLinks', $rs);
        $this->assertArrayHasKey('entity', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}
