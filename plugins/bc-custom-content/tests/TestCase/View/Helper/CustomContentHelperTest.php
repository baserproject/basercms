<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomEntryFactory;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\View\Helper\CustomContentHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Blog helper library.
 *
 * @property CustomContentHelper $CustomContentHelper
 */
class CustomContentHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View($this->getRequest());
        $this->CustomContentHelper = new CustomContentHelper($view);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getTitle
     */
    public function test_getTitle()
    {
        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //currentContentをセット
        $customContentsService = $this->getService(CustomContentsServiceInterface::class);
        $customContent = $customContentsService->get(1);
        $view = new View($this->getRequest()->withAttribute('currentContent', $customContent->content));
        $this->CustomContentHelper = new CustomContentHelper($view);

        //対象メソッドをコール
        $rs = $this->CustomContentHelper->getTitle();
        //戻り値を確認
        $this->assertEquals('サービスタイトル', $rs);
    }

    /**
     * test descriptionExists
     */
    public function test_descriptionExists()
    {
        //check description exists
        $customContent = CustomContentFactory::make([
            'id' => 1,
            'description' => 'test',
            'content' => ContentFactory::make([
                'plugin' => 'BcCustomContent',
                'type' => 'CustomContent',
                'site_id' => 1,
                'entity_id' => 4,
            ])->getEntity()
        ])->getEntity();
        $rs = $this->CustomContentHelper->descriptionExists($customContent);
        //check result value
        $this->assertTrue($rs);
        //check description not exists
        $customContent = CustomContentFactory::make([
            'id' => 1,
            'description' => null,
            'content' => ContentFactory::make([
                'plugin' => 'BcCustomContent',
                'type' => 'CustomContent',
                'site_id' => 1,
                'entity_id' => 4,
            ])->getEntity()
        ])->getEntity();
        $rs = $this->CustomContentHelper->descriptionExists($customContent);
        //check result value
        $this->assertFalse($rs);
    }

    /**
     * test getDescription
     */
    public function test_getDescription()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test getEntryTitle
     */
    public function test_getEntryTitle()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        $customEntriesService->setup(1);

        //対象メソッドをコール
        $rs = $this->CustomContentHelper->getEntryTitle($customEntriesService->get(1));
        //戻り値を確認
        $this->assertEquals('Webエンジニア・Webプログラマー', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getPublished
     */
    public function test_getPublished()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        $customEntriesService->setup(1);

        //対象メソッドをコール
        $rs = $this->CustomContentHelper->getPublished($customEntriesService->get(1));
        //戻り値を確認
        $this->assertEquals('2023-01-30 07:09:22', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getFieldTitle
     */
    public function test_getFieldTitle()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContentsService = $this->getService(CustomContentsServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        $rs = $this->CustomContentHelper->getFieldTitle($customContentsService->get(1), 'recruit_category');
        //戻り値を確認
        $this->assertEquals('求人分類', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test getFieldValue
     */
    public function test_getFieldValue()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test getLink
     */
    public function test_getLink()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        //$fieldNameが存在した場合、
        $rs = $this->CustomContentHelper->getLink(1, 'recruit_category');
        //戻り値を確認
        $this->assertEquals('recruit_category', $rs->name);
        $this->assertEquals('求人分類', $rs->title);

        //$fieldNameが存在しない場合、
        $rs = $this->CustomContentHelper->getLink(1, 'category');
        //戻り値を確認
        $this->assertFalse($rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test getField
     */
    public function test_getField()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        //$fieldNameが存在した場合、
        $rs = $this->CustomContentHelper->getField(1, 'recruit_category');
        $this->assertEquals('recruit_category', $rs->name);

        //$fieldNameが存在しない場合、
        $rs = $this->CustomContentHelper->getField(1, 'category');
        $this->assertFalse($rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test isLoop
     */
    public function test_isLoop()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories'
        ]);
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        CustomFieldFactory::make([
            'id' => 1,
            'name' => 'recruit_category',
            'type' => 'group'
        ])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'use_loop' => true,
            'name' => 'recruit_category'
        ])->persist();
        CustomEntryFactory::make([
            'id' => 1,
            'custom_table_id' => 1
        ])->persist();

        $customEntriesService->setup(1);

        //trueを返す場合
        $rs = $this->CustomContentHelper->isLoop($customEntriesService->get(1), 'recruit_category');
        $this->assertTrue($rs);

        //falseを返す場合
        $rs = $this->CustomContentHelper->isLoop($customEntriesService->get(1), 'recruit');
        $this->assertFalse($rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getLinks
     */
    public function test_getLinks()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        CustomLinkFactory::make([
            'id' => 3,
            'custom_table_id' => 1,
            'custom_field_id' => 2,
            'parent_id' => 2,
            'lft' => 2,
            'rght' => 3,
        ])->persist();

        //$isThreaded = false 場合、
        $rs = $this->CustomContentHelper->getLinks(1, false);
        //戻り値を確認
        $this->assertCount(3, $rs);
        //親子関係のデータが取得しないか確認すること
        $customLinks = $rs->toArray();
        $this->assertArrayNotHasKey('children', $customLinks[1]);

        //$isThreaded = true 場合、
        $rs = $this->CustomContentHelper->getLinks(1);
        //戻り値を確認
        $this->assertCount(2, $rs);
        //親子関係のデータが取得できるか確認すること
        $customLinks = $rs->toArray();
        $this->assertCount(1, $customLinks[1]->children);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test getLinkChildren
     */
    public function test_getLinkChildren()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test clearCacheLinks
     */
    public function test_clearCacheLinks()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test isDisplayField
     */
    public function test_isDisplayField()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        $customEntriesService->setup(1);

        //Trueを返す場合
        $rs = $this->CustomContentHelper->isDisplayField($customEntriesService->get(1), 'recruit_category');
        $this->assertTrue($rs);

        //Falseを返す場合
        $rs = $this->CustomContentHelper->isDisplayField($customEntriesService->get(1), 'feature');
        $this->assertFalse($rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}
