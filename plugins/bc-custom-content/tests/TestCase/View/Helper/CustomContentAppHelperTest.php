<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomEntryFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\View\Helper\CustomContentAppHelper;
use Cake\Core\Configure;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomContentAppHelperTest extends BcTestCase
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
        $this->CustomContentAppHelper = new CustomContentAppHelper(new View());
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
     * test loadPluginHelper
     */
    public function test_loadPluginHelper()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }
    /**
     * test isEnableField
     *
     */
    public function test_isEnableField()
    {
        /**
         * case customField is not exists
         */
        $customLink = CustomLinkFactory::make([
            'name' => 'custom link'
        ])->getEntity();
        $rs = $this->CustomContentAppHelper->isEnableField($customLink);
        $this->assertFalse($rs);
        /**
         * case customField is exists
         * and children is empty
         */
        $customLink['customField'] = [
            'type' => 'group'
        ];
        $rs = $this->CustomContentAppHelper->isEnableField($customLink);
        $this->assertFalse($rs);
        /**
         * case customField is exists
         * and children is not empty
         */
        $customLink['status'] = 1;
        $customLink['children'] = ['name' => 'child'];
        $customLink['custom_field'] = new CustomLink(['type' => 'group']);
        $rs = $this->CustomContentAppHelper->isEnableField($customLink);
        $this->assertTrue($rs);
    }

    /**
     * test getEntryUrl
     *
     */
    public function test_getEntryUrl()
    {
        $siteUrl = Configure::read('BcEnv.siteUrl');
        //サービスクラス
        $this->loadFixtureScenario(SitesScenario::class);
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        $customContentsService = $this->getService(CustomContentsServiceInterface::class);
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
        //customContent
        $customContent = $customContentsService->get(1);
        //view
        $view = new View($this->getRequest()->withAttribute('currentContent', $customContent->content));
        $this->CustomContentAppHelper = new CustomContentAppHelper($view);
        //case CustomEntry name is not empty
        $rs = $this->CustomContentAppHelper->getEntryUrl($customEntriesService->get(1));
        //check result return
        $this->assertEquals($siteUrl . 'test/view/プログラマー', $rs);
        /**
         * case CustomEntry name is null
         */
        CustomEntryFactory::make([
            'id' => 4,
            'custom_table_id' => 1,
            'name' => null
        ])->persist();
        $rs = $this->CustomContentAppHelper->getEntryUrl($customEntriesService->get(4));
        //check result return
        $this->assertEquals($siteUrl . 'test/view/4', $rs);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test searchControl
     */
    public function test_searchControl()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test isDisplayEntrySearch
     */
    public function test_isDisplayEntrySearchFront()
    {
        $customLink = CustomLinkFactory::make([
            'search_target_front' => true,
            'search_target_admin' => false,
            'children' => ['child1'],
            'status' => true
        ])->getEntity();
        $customLink['custom_field'] = new CustomLink(['type' => 'text']);
        $rs = $this->CustomContentAppHelper->isDisplayEntrySearch($customLink, 'front');
        $this->assertTrue($rs);
    }

    public function test_isDisplayEntrySearchAdmin()
    {
        $customLink = CustomLinkFactory::make([
            'search_target_front' => false,
            'search_target_admin' => true,
            'children' => ['child1'],
            'status' => true
        ])->getEntity();
        $customLink['custom_field'] = new CustomLink(['type' => 'text']);
        $rs = $this->CustomContentAppHelper->isDisplayEntrySearch($customLink, 'admin');
        $this->assertTrue($rs);
    }

    public function test_isDisplayEntrySearchInvalidType()
    {
        $customLink = CustomLinkFactory::make([
            'search_target_front' => false,
            'search_target_admin' => false,
            'children' => ['child1'],
            'status' => true
        ])->getEntity();
        $customLink['custom_field'] = new CustomLink(['type' => 'text']);
        $rs = $this->CustomContentAppHelper->isDisplayEntrySearch($customLink, 'invalid');
        $this->assertFalse($rs);
    }

    public function test_isDisplayEntrySearchNoCustomField()
    {
        $customLink = CustomLinkFactory::make([
            'search_target_front' => false,
            'search_target_admin' => false,
            'children' => ['child1'],
            'status' => true
        ])->getEntity();
        $customLink['custom_field'] = null;
        $rs = $this->CustomContentAppHelper->isDisplayEntrySearch($customLink);
        $this->assertFalse($rs);
    }

    public function test_isDisplayEntrySearchGroupTypeNoChildren()
    {
        $customLink = CustomLinkFactory::make([
            'search_target_front' => false,
            'search_target_admin' => false,
            'children' => [],
            'status' => true
        ])->getEntity();
        $customLink['custom_field'] = new CustomLink(['type' => 'group']);
        $rs = $this->CustomContentAppHelper->isDisplayEntrySearch($customLink);
        $this->assertFalse($rs);
    }

    public function test_isDisplayEntrySearchFileType()
    {
        $customLink = CustomLinkFactory::make([
            'search_target_front' => true,
            'search_target_admin' => false,
            'children' => ['child1'],
            'status' => true
        ])->getEntity();
        $customLink['custom_field']['type'] = 'file';
        $rs = $this->CustomContentAppHelper->isDisplayEntrySearch($customLink);
        $this->assertFalse($rs);
    }
}
