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
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
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
        $this->markTestIncomplete('このテストはまだ実装されていません。');
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
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test getPublished
     */
    public function test_getPublished()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test getFieldTitle
     */
    public function test_getFieldTitle()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
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
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test getField
     */
    public function test_getField()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test isLoop
     */
    public function test_isLoop()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
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

        //$isThreaded = false 場合、
        $rs = $this->CustomContentHelper->getLinks(1, false);
        //戻り値を確認
        $this->assertCount(2, $rs);

        //$isThreaded = true 場合、
        $rs = $this->CustomContentHelper->getLinks(1);
        //戻り値を確認
        $this->assertCount(2, $rs);

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
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

}
