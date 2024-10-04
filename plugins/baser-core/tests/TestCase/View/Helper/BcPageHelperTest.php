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

namespace BaserCore\Test\TestCase\View\Helper;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcPageHelper;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcPage helper library.
 */
class BcPageHelperTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminAppView = new BcAdminAppView($this->getRequest(), null, null, [
            'name' => 'Pages',
            'plugin' => 'BaserCore'
        ]);
        $this->BcPage = new BcPageHelper($this->BcAdminAppView);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcPage);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcPage->ContentsService);
    }

    /**
     * ページ機能用URLを取得する
     *
     * @param array $pageId 固定ページID
     * @param array $expected 期待値
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($pageId, $expected)
    {
        //データ生成
        ContentFactory::make(['url' => '/index', 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 1])->persist();
        ContentFactory::make(['url' => '/service/index', 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 2])->persist();
        ContentFactory::make(['url' => '/service/about', 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 3])->persist();
        ContentFactory::make(['url' => '/icons', 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 4])->persist();
        ContentFactory::make(['url' => '/sitemap', 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 5])->persist();
        ContentFactory::make(['url' => '/m/index', 'plugin' => 'BaserCore', 'type' => 'Page', 'entity_id' => 6])->persist();

        PageFactory::make(['id' => 1])->persist();
        PageFactory::make(['id' => 2])->persist();
        PageFactory::make(['id' => 3])->persist();
        PageFactory::make(['id' => 4])->persist();
        PageFactory::make(['id' => 5])->persist();
        PageFactory::make(['id' => 6])->persist();
        PageFactory::make(['id' => 7])->persist();

        $pageService = $this->getService(PagesServiceInterface::class);

        //実行
        $result = $this->BcPage->getUrl($pageService->get($pageId));
        $this->assertEquals($expected, $result);
    }

    public static function getUrlDataProvider()
    {
        return [
            [1, '/index'],
            [2, '/service/index'],
            [3, '/service/about'],
            [4, '/icons'],
            [5, '/sitemap'],
            [6, '/m/index']
        ];
    }

    public static function allowPublishDataProvider()
    {
        return [
            [true, 0, 0, true, 'statusの値がそのままかえってきません'],
            [true, '2200-1-1', 0, false, '公開開始日時の前に公開されています'],
            [true, 0, '1999-1-1', false, '公開終了日時の後に公開されています'],
            [true, '2199-1-1', '2200-1-1', false, '公開開始日時の前に公開されています'],
            [true, '1999-1-1', '2000-1-1', false, '公開開始日時の後に公開されています'],
            [false, '1999-1-1', 0, false, '非公開になっていません'],
        ];
    }

    /**
     * ページリストを取得する
     *
     * @dataProvider getPageListDataProvider
     */
    public function testGetPageList($id, $expects)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcPage->GetPageList($id);
        $result = Hash::extract($result, '{n}.Content.type');
        $this->assertEquals($expects, $result);
    }

    public static function getPageListDataProvider()
    {
        return [
            [1, ['Page', 'Page', 'Page', 'Page', 'ContentFolder']],    // トップフォルダ
            [21, ['Page', 'Page', 'Page', 'ContentFolder']],    // 下層フォルダ
            [4, []]    // ターゲットがフォルダでない
        ];
    }

}
