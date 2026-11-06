<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Test\TestCase\View\Helper;

use BaserCore\Routing\Route\BcContentsRoute;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BcBlog\Test\Factory\BlogPostFactory;
use BcCustomContent\Test\Factory\CustomContentFactory;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcSeo\Test\Factory\SeoMetaFactory;
use BcSeo\View\Helper\SeoHelper;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * SeoHelperTest
 */
class SeoHelperTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    /**
     * testInitialize
     *
     * canonicalUrl に false を入れるとコアのカノニカル出力が止まるため、変更しない
     */
    public function testInitialize()
    {
        $view = new View();
        $seoHelper = new SeoHelper($view);
        $seoHelper->initialize([]);
        $this->assertNull($view->get('canonicalUrl'));
    }

    /**
     * testBeforeRender
     */
    public function testBeforeRender()
    {
        // カノニカル URL の設定があれば canonicalUrl に渡す
        $view = new View($this->getRequest('/'));
        $seoHelper = $this->getMockBuilder(SeoHelper::class)
            ->setConstructorArgs([$view])
            ->onlyMethods(['getMeta'])
            ->getMock();
        $seoHelper->method('getMeta')->willReturn(['canonical_url' => ['value' => 'https://example.com/canonical']]);
        $seoHelper->beforeRender(new Event('View.beforeRender', $view), '');
        $this->assertEquals('https://example.com/canonical', $view->get('canonicalUrl'));

        // 設定が無ければ変更しない
        $view = new View($this->getRequest('/'));
        $seoHelper = $this->getMockBuilder(SeoHelper::class)
            ->setConstructorArgs([$view])
            ->onlyMethods(['getMeta'])
            ->getMock();
        $seoHelper->method('getMeta')->willReturn(['canonical_url' => ['value' => '']]);
        $seoHelper->beforeRender(new Event('View.beforeRender', $view), '');
        $this->assertNull($view->get('canonicalUrl'));

        // 管理画面では何もしない
        $view = new View($this->getRequest('/baser/admin'));
        $seoHelper = $this->getMockBuilder(SeoHelper::class)
            ->setConstructorArgs([$view])
            ->onlyMethods(['getMeta'])
            ->getMock();
        $seoHelper->expects($this->never())->method('getMeta');
        $seoHelper->beforeRender(new Event('View.beforeRender', $view), '');
        $this->assertNull($view->get('canonicalUrl'));
    }

    /**
     * testMeta
     */
    public function testMeta()
    {
        $request = $this->getRequest('/');
        $view = new View($request);
        $view->loadHelper('BaserCore.BcBaser');
        $view->loadHelper('BaserCore.BcHtml');
        $seoHelper = new SeoHelper($view);

        $view->set('description', 'test description');
        $view->set('keywords', 'test keywords');

        ob_start();
        $seoHelper->meta();
        $result = ob_get_clean();
        $this->assertEquals("<meta name=\"description\" content=\"test description\">\n<meta name=\"keywords\" content=\"test keywords\">\n", $result);

        ob_start();
        $seoHelper->meta(['description']);
        $result = ob_get_clean();
        $this->assertEquals("<meta name=\"description\" content=\"test description\">\n", $result);
    }

    /**
     * testGetMeta
     */
    public function testGetMeta()
    {
        $view = new View();
        $seoHelper = new SeoHelper($view);

        $view->set('description', 'test description');
        $view->set('keywords', 'test keywords');
        $meta = $seoHelper->getMeta();
        $this->assertEquals('test description', $meta['description']['value']);
        $this->assertEquals('test keywords', $meta['keywords']['value']);
    }

    /**
     * testGetMetaBlogPost
     */
    public function testGetMetaBlogPost()
    {
        ContentFactory::make([
            'id' => 1,
            'site_id' => 1,
            'url' => '/test'
        ])->persist();
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $content = $contentsTable->get(1, contain: ['Sites']);
        $blogPost = BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'Test Post',
        ])->persist();

        SeoMetaFactory::make([
            'table_alias' => 'Sites',
            'entity_id' => 1,
            'description' => 'site description',
            'keywords' => 'site keywords',
        ])->persist();
        SeoMetaFactory::make([
            'table_alias' => 'Contents',
            'entity_id' => 1,
            'description' => 'blogContent description',
            'og_type' => 'blogContent og_type',
        ])->persist();
        SeoMetaFactory::make([
            'table_alias' => 'BlogPosts',
            'entity_id' => 1,
            'description' => 'blogPost description',
        ])->persist();

        $route = new BcContentsRoute('/test', []);
        $request = $this->getRequest('/test/archives/1');
        $request = $request->withAttribute('route', $route);
        $request = $request->withAttribute('currentContent', $content);
        $request = $request->withParam('plugin', 'BcBlog');
        $request = $request->withParam('controller', 'Blog');
        $request = $request->withParam('action', 'archives');
        $view = new View($request);
        $view->set('post', $blogPost);

        $seoHelper = new SeoHelper($view);
        $meta = $seoHelper->getMeta();
        $this->assertEquals('blogPost description', $meta['description']['value']);
        $this->assertEquals('site keywords', $meta['keywords']['value']);
        $this->assertEquals('blogContent og_type', $meta['og_type']['value']);
    }

    /**
     * testGetMetaCustomContentArchives
     *
     * 関連データのアーカイブでは、関連先エントリーで設定した項目のみ出力し、親の設定は引き継がない
     */
    public function testGetMetaCustomContentArchives()
    {
        $this->createCustomContentArchivesData();
        SeoMetaFactory::make([
            'table_alias' => 'CustomEntries',
            'table_id' => 2,
            'entity_id' => 7,
            'description' => 'category description',
        ])->persist();

        $seoHelper = new SeoHelper($this->getCustomContentArchivesView('recruit_category', '7'));
        $meta = $seoHelper->getMeta();
        $this->assertEquals('category description', $meta['description']['value']);
        // 関連先エントリーで未設定の項目は、サイト・コンテンツの設定も出力しない
        $this->assertArrayNotHasKey('keywords', $meta);
        $this->assertArrayNotHasKey('og_description', $meta);
        $this->assertArrayNotHasKey('og_image', $meta);
    }

    /**
     * testGetMetaCustomContentArchivesWithoutSeoMeta
     *
     * 関連先エントリーにSEO設定がない場合は何も出力しない
     */
    public function testGetMetaCustomContentArchivesWithoutSeoMeta()
    {
        $this->createCustomContentArchivesData();

        $seoHelper = new SeoHelper($this->getCustomContentArchivesView('recruit_category', '7'));
        $this->assertEquals([], $seoHelper->getMeta());
    }

    /**
     * testGetMetaCustomContentArchivesNotRelatedField
     *
     * 関連データ以外のフィールドのアーカイブでは、従来どおりサイト・コンテンツの設定を出力する
     */
    public function testGetMetaCustomContentArchivesNotRelatedField()
    {
        $this->createCustomContentArchivesData();

        $seoHelper = new SeoHelper($this->getCustomContentArchivesView('employment_status', '1'));
        $meta = $seoHelper->getMeta();
        $this->assertEquals('content description', $meta['description']['value']);
        $this->assertEquals('site keywords', $meta['keywords']['value']);
        $this->assertEquals('content og_description', $meta['og_description']['value']);
    }

    /**
     * カスタムコンテンツのアーカイブ用のデータを作成する
     *
     * - カスタムコンテンツ（コンテンツID: 1）は、カスタムテーブル1 を利用
     * - カスタムテーブル1 の recruit_category は、カスタムテーブル2 を関連先とする関連データ
     * - カスタムテーブル1 の employment_status は、ラジオボタン
     * - サイト・コンテンツにSEO設定あり
     */
    private function createCustomContentArchivesData(): void
    {
        ContentFactory::make([
            'id' => 1,
            'site_id' => 1,
            'url' => '/test/',
            'plugin' => 'BcCustomContent',
            'type' => 'CustomContent',
            'entity_id' => 1,
        ])->persist();
        CustomContentFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
        ])->persist();
        CustomFieldFactory::make([
            'id' => 1,
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'meta' => '{"BcCcRelated":{"custom_table_id":"2","filter_name":"","filter_value":""}}',
        ])->persist();
        CustomFieldFactory::make([
            'id' => 2,
            'name' => 'employment_status',
            'type' => 'BcCcRadio',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'name' => 'recruit_category',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'custom_table_id' => 1,
            'custom_field_id' => 2,
            'name' => 'employment_status',
        ])->persist();

        SeoMetaFactory::make([
            'table_alias' => 'Sites',
            'entity_id' => 1,
            'description' => 'site description',
            'keywords' => 'site keywords',
        ])->persist();
        SeoMetaFactory::make([
            'table_alias' => 'Contents',
            'entity_id' => 1,
            'description' => 'content description',
            'og_description' => 'content og_description',
        ])->persist();
    }

    /**
     * カスタムコンテンツのアーカイブを表示する View を取得する
     *
     * @param string $field フィールド名
     * @param string $value 値
     */
    private function getCustomContentArchivesView(string $field, string $value): View
    {
        $content = TableRegistry::getTableLocator()->get('BaserCore.Contents')->get(1, contain: ['Sites']);
        $customContent = TableRegistry::getTableLocator()->get('BcCustomContent.CustomContents')->get(1);

        $request = $this->getRequest("/test/archives/{$field}/{$value}");
        $request = $request->withAttribute('route', new BcContentsRoute('/test/', []));
        $request = $request->withAttribute('currentContent', $content);
        $request = $request->withParam('plugin', 'BcCustomContent');
        $request = $request->withParam('controller', 'CustomContent');
        $request = $request->withParam('action', 'archives');
        $request = $request->withParam('pass', [$field, $value]);
        $view = new View($request);
        $view->set('customContent', $customContent);
        return $view;
    }
}
