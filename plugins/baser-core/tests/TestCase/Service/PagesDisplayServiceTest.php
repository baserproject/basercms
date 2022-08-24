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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PagesDisplayService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PagesDisplayServiceTest
 * @property PagesDisplayService $PagesDisplayService
 */
class PagesDisplayServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.ContentFolders',
        'plugin.BcSearchIndex.SearchIndexes',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PagesDisplayService = new PagesDisplayService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PagesDisplayService);
        parent::tearDown();
    }

    /**
     * ポストデータが存在する場合のgetPreviewData
     * @param  string $url リクエストのURL
     * @param  string $previewType
     * @param  string $tmp プレビュー用のデータ
     * @param  bool $isError エラーが出る前提かどうか
     * @return void
     * @dataProvider getPreviewDataDataProvider
     */
    public function testGetPreviewData($url, $previewType, $tmp, $isError)
    {
        $request = $this->getRequest($url . $previewType);
        $page = $this->PagesDisplayService->Pages->find()->contain('Contents')->first();
        $page->{$previewType === 'default' ? 'contents_tmp' : $previewType } = $tmp;
        $request = $request->withData('Page', $page->toArray());
        if ($isError) {
            $this->expectException("Cake\Http\Exception\NotFoundException");
            $this->expectExceptionMessage("本稿欄でスクリプトの入力は許可されていません。");
        }
        $pagesDisplay = $this->PagesDisplayService->getPreviewData($request);
        $this->assertEquals($pagesDisplay['contents'], $tmp);
    }

    public function getPreviewDataDataProvider()
    {
        $mainSite = '/baser/admin/baser-core/preview/view?url=https://localhost/test&preview=';
        $subSite = '/baser/admin/baser-core/preview/view?url=https://localhost/en/サイトID3の固定ページ&preview=';
        return [
            [$mainSite, 'default', '<p>test</p>', false],
            [$mainSite, 'draft', '<p>test</p>', false],
            [$mainSite, 'draft', '<script type="text/javascript">', true],
            [$subSite, 'default', '<p>test</p>', false],
            [$subSite, 'draft', '<p>test</p>', false],
            [$subSite, 'draft', '<script type="text/javascript">', true],
        ];
    }

    /**
     * ポストデータが存在しない場合のGetPreviewData
     *
     * @return void
     */
    public function testGetPreviewDataWithoutData()
    {
        foreach([
            '/baser/admin/baser-core/preview/view?url=https://localhost/test&preview=default',
            '/baser/admin/baser-core/preview/view?url=https://localhost/en/サイトID3の固定ページ&preview=default'
            ] as $url) {
            $request = $this->getRequest($url);
            $this->expectException("Cake\Http\Exception\NotFoundException");
            $this->expectExceptionMessage("プレビューが適切ではありません。");
            $pagesDisplay = $this->PagesDisplayService->getPreviewData($request);
        }
    }
}
