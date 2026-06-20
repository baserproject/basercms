<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.7
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp\BcBlog;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcBlog\Test\Scenario\BlogPostsAdminServiceScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcMcp\Mcp\BcBlog\BlogPostsTool;

/**
 * BlogPostsToolTest
 */
class BlogPostsToolTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var \BcMcp\Mcp\BcBlog\BlogPostsTool
     */
    protected $BlogPostsTool;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogPostsTool = new BlogPostsTool();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BlogPostsTool);
        parent::tearDown();
    }

    /**
     * test BlogPostsTool instantiation
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf(BlogPostsTool::class, $this->BlogPostsTool);
    }

    /**
     * test addBlogPost
     */
    public function testAddBlogPost()
    {
        // テストデータが無い環境でも、メソッドが存在することを確認
        $this->assertTrue(method_exists($this->BlogPostsTool, 'addBlogPost'));

        // エラーの場合でも結果が配列で返されることを確認
        $result = $this->BlogPostsTool->addBlogPost(
            'テストブログ記事',
            'これはテスト用のブログ記事です。',
            'news',
            null,
            'test@example.com'
        );

        $this->assertIsArray($result);
        // ブログ記事が追加されたかどうかの確認
        // エラーが発生した場合はcontentキーにエラーメッセージが含まれる
        if (isset($result['content']) && is_string($result['content'])) {
            // エラーケース
            $this->assertIsString($result['content']);
        } else {
            // 成功ケース
            $this->assertArrayHasKey('id', $result);
        }
    }

    /**
     * test getBlogPosts
     */
    public function testGetBlogPosts()
    {
        BlogPostFactory::make([
            'id' => 1,
        ])->persist();
        $result = $this->BlogPostsTool->getBlogPosts(1);

        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }

    /**
     * test getBlogPosts with keyword search
     */
    public function testGetBlogPostsWithKeyword()
    {
        // テスト用のブログ記事を作成
        BlogPostFactory::make([
            'id' => 1,
            'title' => 'テストブログ記事',
            'detail' => 'これはテスト用の詳細です。',
            'content' => 'テスト用の概要内容です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-01 00:00:00'
        ])->persist();

        BlogPostFactory::make([
            'id' => 2,
            'title' => '別の記事',
            'detail' => '別の内容です。',
            'content' => '別の概要です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-02 00:00:00'
        ])->persist();

        BlogPostFactory::make([
            'id' => 3,
            'title' => 'サンプル記事',
            'detail' => 'テストという単語が含まれる詳細です。',
            'content' => 'サンプル概要です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-03 00:00:00'
        ])->persist();

        // キーワード検索のテスト（"テスト"で検索）
        $result = $this->BlogPostsTool->getBlogPosts(1, 'テスト');

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        // キーワードに一致する記事が取得されることを確認
        // "テスト"という単語がタイトルまたは詳細に含まれる記事が検索される
        $this->assertGreaterThan(0, count($result['data']));

        // 検索結果の構造を確認
        if (count($result['data']) > 0) {
            $firstPost = $result['data'][0];
            $this->assertArrayHasKey('id', $firstPost);
            $this->assertArrayHasKey('title', $firstPost);
            $this->assertArrayHasKey('detail', $firstPost);
        }
    }

    /**
     * test getBlogPosts with keyword search no results
     */
    public function testGetBlogPostsWithKeywordNoResults()
    {
        // テスト用のブログ記事を作成
        BlogPostFactory::make([
            'id' => 1,
            'title' => 'サンプル記事',
            'detail' => 'サンプルの詳細です。',
            'content' => 'サンプル概要です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-01 00:00:00'
        ])->persist();

        // 存在しないキーワードで検索
        $result = $this->BlogPostsTool->getBlogPosts(1, '存在しないキーワード');

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        // 検索結果が0件であることを確認
        $this->assertEquals(0, count($result['data']));
    }

    /**
     * test getBlogPosts with empty keyword
     */
    public function testGetBlogPostsWithEmptyKeyword()
    {
        // テスト用のブログ記事を作成
        BlogPostFactory::make([
            'id' => 1,
            'title' => 'テスト記事',
            'detail' => 'テストの詳細です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-01 00:00:00'
        ])->persist();

        // 空のキーワードで検索（すべての記事が取得される）
        $result = $this->BlogPostsTool->getBlogPosts(1, '');

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('data', $result);

        // 記事が取得されることを確認
        $this->assertGreaterThan(0, count($result['data']));
    }

    /**
     * test getBlogPosts with limit parameter
     */
    public function testGetBlogPostsWithLimit()
    {
        // 5つのテスト記事を作成
        for($i = 1; $i <= 5; $i++) {
            BlogPostFactory::make([
                'id' => $i,
                'title' => "テスト記事 {$i}",
                'detail' => "テスト記事 {$i} の詳細です。",
                'content' => "テスト記事 {$i} の概要です。",
                'blog_content_id' => 1,
                'status' => 1,
                'posted' => "2023-01-0{$i} 00:00:00"
            ])->persist();
        }

        // limit = 3 でテスト
        $result = $this->BlogPostsTool->getBlogPosts(1, null, null, 3, 1);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        // limitが正しく適用されていることを確認
        $this->assertLessThanOrEqual(3, count($result['data']));
        $this->assertEquals(3, $result['pagination']['limit']);
    }

    /**
     * test getBlogPosts with page parameter
     */
    public function testGetBlogPostsWithPage()
    {
        // 10個のテスト記事を作成
        for($i = 1; $i <= 10; $i++) {
            BlogPostFactory::make([
                'id' => $i,
                'title' => "ページテスト記事 {$i}",
                'detail' => "ページテスト記事 {$i} の詳細です。",
                'content' => "ページテスト記事 {$i} の概要です。",
                'blog_content_id' => 1,
                'status' => 1,
                'posted' => sprintf("2023-01-%02d 00:00:00", $i)
            ])->persist();
        }

        // 1ページ目（limit=3）
        $result1 = $this->BlogPostsTool->getBlogPosts(1, null, null, 3, 1);

        $this->assertArrayHasKey('data', $result1);
        $this->assertArrayHasKey('pagination', $result1);
        $this->assertArrayHasKey('data', $result1);
        $this->assertArrayHasKey('pagination', $result1);

        $this->assertEquals(1, $result1['pagination']['page']);
        $this->assertEquals(3, $result1['pagination']['limit']);
        $this->assertLessThanOrEqual(3, count($result1['data']));

        // 2ページ目（limit=3）
        $result2 = $this->BlogPostsTool->getBlogPosts(1, null, null, 3, 2);

        $this->assertArrayHasKey('data', $result2);
        $this->assertArrayHasKey('pagination', $result2);
        $this->assertArrayHasKey('data', $result2);
        $this->assertArrayHasKey('pagination', $result2);

        $this->assertEquals(2, $result2['pagination']['page']);
        $this->assertEquals(3, $result2['pagination']['limit']);
        $this->assertLessThanOrEqual(3, count($result2['data']));

        // 1ページ目と2ページ目で異なる記事が取得されることを確認
        if (count($result1['data']) > 0 && count($result2['data']) > 0) {
            $firstPageIds = array_column($result1['data'], 'id');
            $secondPageIds = array_column($result2['data'], 'id');

            // 1ページ目と2ページ目のIDに重複がないことを確認
            $intersection = array_intersect($firstPageIds, $secondPageIds);
            $this->assertEmpty($intersection, '1ページ目と2ページ目で同じ記事が重複して取得されています');
        }
    }

    /**
     * test getBlogPosts with limit and page combination
     */
    public function testGetBlogPostsWithLimitAndPage()
    {
        // 8個のテスト記事を作成
        for($i = 1; $i <= 8; $i++) {
            BlogPostFactory::make([
                'id' => $i,
                'title' => "組み合わせテスト記事 {$i}",
                'detail' => "組み合わせテスト記事 {$i} の詳細です。",
                'content' => "組み合わせテスト記事 {$i} の概要です。",
                'blog_content_id' => 1,
                'status' => 1,
                'posted' => sprintf("2023-01-%02d 00:00:00", $i)
            ])->persist();
        }

        // limit=2, page=3 のテスト（5〜6番目の記事が取得される想定）
        $result = $this->BlogPostsTool->getBlogPosts(1, null, null, 2, 3);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);

        // パラメータが正しく設定されていることを確認
        $this->assertEquals(3, $result['pagination']['page']);
        $this->assertEquals(2, $result['pagination']['limit']);
        $this->assertLessThanOrEqual(2, count($result['data']));
    }

    /**
     * test getBlogPosts with invalid page number
     */
    public function testGetBlogPostsWithInvalidPageNumber()
    {
        // 2個のテスト記事を作成
        BlogPostFactory::make([
            'id' => 1,
            'title' => '無効ページテスト記事1',
            'detail' => '無効ページテスト記事1の詳細です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-01 00:00:00'
        ])->persist();

        BlogPostFactory::make([
            'id' => 2,
            'title' => '無効ページテスト記事2',
            'detail' => '無効ページテスト記事2の詳細です。',
            'blog_content_id' => 1,
            'status' => 1,
            'posted' => '2023-01-02 00:00:00'
        ])->persist();

        // 存在しないページ番号（page=10）でテスト
        $result = $this->BlogPostsTool->getBlogPosts(1, null, null, 10, 10);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('data', $result);

        // 存在しないページの場合、データが空であることを確認
        $this->assertEquals(0, count($result['data']));
    }

    /**
     * test getBlogPost
     */
    public function testGetBlogPost()
    {
        $this->loadFixtureScenario(BlogPostsAdminServiceScenario::class);
        $result = $this->BlogPostsTool->getBlogPost(1);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals(1, $result['id']);
    }

    /**
     * test editBlogPost
     */
    public function testEditBlogPost()
    {
        $this->loadFixtureScenario(BlogPostsAdminServiceScenario::class);
        $result = $this->BlogPostsTool->editBlogPost(
            1,
            '更新されたタイトル',
            '更新された詳細',
            null,
            null,
            null,
            null
        );

        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('更新されたタイトル', $result['title']);
        $this->assertEquals('更新された詳細', $result['detail']);
    }

    /**
     * test deleteBlogPost
     */
    public function testDeleteBlogPost()
    {
        $this->loadFixtureScenario(BlogPostsAdminServiceScenario::class);
        $result = $this->BlogPostsTool->deleteBlogPost(1);

        $this->assertArrayHasKey('message', $result);
    }

    /**
     * test getBlogCategoryId
     */
    public function testGetBlogCategoryId()
    {
        BlogCategoryFactory::make([
            'name' => 'プログラム',
            'blog_content_id' => 1,
        ])->persist();
        $categoryId = $this->execPrivateMethod($this->BlogPostsTool, 'getBlogCategoryId', ['プログラム', 1]);

        $this->assertIsInt($categoryId);
        $this->assertGreaterThan(0, $categoryId);
    }

    /**
     * test getBlogContentId
     */
    public function testGetBlogContentId()
    {
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1,
            null,
            'test-blog',
            'test-blog-url',
        );
        $contentId = $this->execPrivateMethod($this->BlogPostsTool, 'getBlogContentId', ['test-blog']);

        $this->assertIsInt($contentId);
        $this->assertGreaterThan(0, $contentId);
    }

    /**
     * test processFileUpload with base64 data
     */
    public function testProcessFileUploadWithBase64()
    {
        // 小さなPNG画像のbase64データ（2x2ピクセルの赤いPNG）
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAFElEQVQIHWP8//8/AzYwOjr6PxQAAP//DyGg5r8AAAAASUVORK5CYII=';

        $result = $this->execPrivateMethod($this->BlogPostsTool, 'processFileUpload', [$base64Data]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('tmp_name', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('ext', $result);

        $this->assertEquals('image/png', $result['type']);
        $this->assertEquals('png', $result['ext']);
        $this->assertEquals(UPLOAD_ERR_OK, $result['error']);

        // 一時ファイルがちゃんと作成されているかチェック
        $this->assertTrue(file_exists($result['tmp_name']));

        // クリーンアップ
        if (file_exists($result['tmp_name'])) {
            unlink($result['tmp_name']);
        }
    }

    /**
     * test processFileUpload with URL
     */
    public function testProcessFileUploadWithUrl()
    {
        $url = 'https://example.com/image.jpg';

        $result = $this->execPrivateMethod($this->BlogPostsTool, 'processFileUpload', [$url]);

        // URLの場合はダウンロードに失敗してfalseが返される（example.comは存在しない画像）
        $this->assertFalse($result);
    }

    /**
     * test processFileUpload with invalid base64 data
     */
    public function testProcessFileUploadWithInvalidBase64()
    {
        // より確実に無効になるbase64データ
        $invalidBase64 = 'invalid_format_data';
        $result = $this->execPrivateMethod($this->BlogPostsTool, 'processFileUpload', [$invalidBase64]);
        // 無効なフォーマットの場合はfalseが返される
        $this->assertFalse($result);
    }

    /**
     * test processUrlFile with invalid URL
     */
    public function testProcessUrlFileWithInvalidUrl()
    {
        $invalidUrl = 'not_a_url';

        try {
            $this->execPrivateMethod($this->BlogPostsTool, 'processUrlFile', [$invalidUrl]);
            $this->fail('例外が投げられるべきです');
        } catch (\Exception $e) {
            $this->assertStringContainsString('不正なURL形式です', $e->getMessage());
        }
    }

    /**
     * test processUrlFile with non-HTTP URL
     */
    public function testProcessUrlFileWithNonHttpUrl()
    {
        $ftpUrl = 'ftp://example.com/file.jpg';

        try {
            $this->execPrivateMethod($this->BlogPostsTool, 'processUrlFile', [$ftpUrl]);
            $this->fail('例外が投げられるべきです');
        } catch (\Exception $e) {
            $this->assertStringContainsString('HTTPまたはHTTPSのURLのみサポートされています', $e->getMessage());
        }
    }

    /**
     * test processBase64File with invalid base64 format
     */
    public function testProcessBase64FileWithInvalidFormat()
    {
        // 正しくないdata:URLフォーマット
        $invalidBase64 = 'data:image/png;base64,not_valid_base64!!!';

        try {
            $this->execPrivateMethod($this->BlogPostsTool, 'processBase64File', [$invalidBase64]);
            $this->fail('例外が投げられるべきです');
        } catch (\Exception $e) {
            $this->assertStringContainsString('base64デコードに失敗しました', $e->getMessage());
        }
    }

    /**
     * test addBlogPost with base64 eyeCatch
     */
    public function testAddBlogPostWithBase64EyeCatch()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make([
            'name' => 'news',
            'type' => 'BlogContent',
            'plugin' => 'BcBlog',
            'site_id' => 1,
            'entity_id' => 1000,
        ])->persist();
        // BlogContentのテストデータを作成
        BlogContentFactory::make([
            'id' => 1000,
            'description' => 'ニュースブログ',
            'template' => 'default',
            'list_count' => 10,
            'list_direction' => 'DESC',
            'feed_count' => 10,
            'tag_use' => false,
            'comment_use' => false,
            'comment_approve' => false,
            'widget_area' => null,
            'eye_catch_size_thumb_width' => 150,
            'eye_catch_size_thumb_height' => 150,
            'eye_catch_size_mobile_thumb_width' => 100,
            'eye_catch_size_mobile_thumb_height' => 100,
            'use_content' => true,
        ])->persist();

        // 2x2ピクセルの小さなPNG画像のbase64データ（テスト済み）
        $base64Data = 'data:image/png;base64,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';

        $result = $this->BlogPostsTool->addBlogPost(
            'テストブログ記事（アイキャッチ付き）',
            'これはアイキャッチ画像付きのテスト記事です。',
            'news', // blogContent
            null, // name
            'これは概要です。', // content
            null, // category
            null, // email
            0,    // status
            '2025/01/01 00:00:00', // posted
            null, // publishBegin
            null, // publishEnd
            $base64Data, // eyeCatch,
            1
        );

        $this->assertIsArray($result);

        // エラーが発生しないことを明確にテスト

        // 成功時のレスポンス内容をテスト
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('テストブログ記事（アイキャッチ付き）', $result['title']);
        $filePath = WWW_ROOT . 'files' . DS . 'blog' . DS . '1000' . DS . 'blog_posts' . DS . $result['eye_catch'];
        $this->assertFileExists($filePath);
        (new BcFolder())->delete(WWW_ROOT . 'files' . DS . 'blog' . DS . '1000');
    }

    public function testAddBlogPostWithUrlEyeCatch()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make([
            'name' => 'news',
            'type' => 'BlogContent',
            'plugin' => 'BcBlog',
            'site_id' => 1,
            'entity_id' => 1000,
        ])->persist();
        // BlogContentのテストデータを作成
        BlogContentFactory::make([
            'id' => 1000,
            'description' => 'ニュースブログ',
            'template' => 'default',
            'list_count' => 10,
            'list_direction' => 'DESC',
            'feed_count' => 10,
            'tag_use' => false,
            'comment_use' => false,
            'comment_approve' => false,
            'widget_area' => null,
            'eye_catch_size_thumb_width' => 150,
            'eye_catch_size_thumb_height' => 150,
            'eye_catch_size_mobile_thumb_width' => 100,
            'eye_catch_size_mobile_thumb_height' => 100,
            'use_content' => true,
        ])->persist();

        $result = $this->BlogPostsTool->addBlogPost(
            'テストブログ記事（アイキャッチ付き）',
            'これはアイキャッチ画像付きのテスト記事です。',
            'news', // blogContent
            null, // name
            'これは概要です。', // content
            null, // category
            null, // email
            0,    // status
            null, // posted
            null, // publishBegin
            null, // publishEnd
            'https://basercms.net/img/basercms_logo.png', // eyeCatch,
            1
        );

        $this->assertIsArray($result);

        // エラーが発生しないことを明確にテスト

        // 成功時のレスポンス内容をテスト
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('テストブログ記事（アイキャッチ付き）', $result['title']);
        $this->assertTrue(isset($result['eye_catch']));
        $filePath = WWW_ROOT . 'files' . DS . 'blog' . DS . '1000' . DS . 'blog_posts' . DS . $result['eye_catch'];
        $this->assertFileExists($filePath);
        (new BcFolder())->delete(WWW_ROOT . 'files' . DS . 'blog' . DS . '1000');
    }

    /**
     * basercms.png をチャンク分割して送信し、その画像を使ってブログ記事を追加するテスト
     */
    public function testAddBlogPostWithChunkedImageUpload()
    {
        // 初期設定とファクトリー設定
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make([
            'name' => 'news',
            'type' => 'BlogContent',
            'plugin' => 'BcBlog',
            'site_id' => 1,
            'entity_id' => 1000,
        ])->persist();
        BlogContentFactory::make(['id' => 1000, 'name' => 'news'])->persist();

        // basercms.pngファイルを読み込み
        $imagePath = WWW_ROOT . 'img' . DS . 'basercms.png';
        $this->assertTrue(file_exists($imagePath), 'basercms.png が存在しません');

        $imageContent = file_get_contents($imagePath);
        $fileId = 'test_blog_image_' . uniqid();
        $filename = 'basercms_blog.png';

        // FileUploadToolのインスタンスを作成
        $fileUploadTool = new \BcMcp\Mcp\BaserCore\FileUploadTool();

        // 画像を1024バイトずつのチャンクに分割
        $chunkSize = 1024;
        $chunks = str_split($imageContent, $chunkSize);
        $totalChunks = count($chunks);

        $this->assertGreaterThan(1, $totalChunks, '画像ファイルが小さすぎてチャンク分割できません');

        // 各チャンクを順番に送信（最後のチャンク以外）
        for($i = 0; $i < $totalChunks - 1; $i++) {
            $result = $fileUploadTool->sendFileChunk($fileId, $i, $totalChunks, base64_encode($chunks[$i]), $filename);

            $this->assertEquals('chunk_received', $result['status']);
            $this->assertEquals($i + 1, $result['progress']);
        }

        // 最後のチャンクを送信
        $lastIndex = $totalChunks - 1;
        $result = $fileUploadTool->sendFileChunk($fileId, $lastIndex, $totalChunks, base64_encode($chunks[$lastIndex]), $filename);

        $this->assertEquals('complete', $result['status']);

        // アップロードされたファイルが正しく作成されていることを確認
        $uploadedFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($uploadedFile), 'アップロードされたファイルが見つかりません');
        $this->assertEquals($imageContent, file_get_contents($uploadedFile), 'アップロードされたファイルの内容が一致しません');

        // アップロードしたファイル名を使ってブログ記事を作成
        $blogResult = $this->BlogPostsTool->addBlogPost(
            'チャンク分割画像付きブログ記事',                    // title
            '<p>チャンク分割でアップロードした画像を使用したテスト記事です。</p>', // detail
            'news',                                         // blogContent
            null,                                           // name
            'チャンク分割画像のテスト概要',                     // content
            null,                                           // category
            null,                                           // email
            1,                                              // status (公開)
            null,                                           // posted
            null,                                           // publishBegin
            null,                                           // publishEnd
            $filename,                                      // eyeCatch (アップロードしたファイル名)
            1                                               // loginUserId
        );

        $this->assertNotEmpty($blogResult['eye_catch'], 'アイキャッチ画像が設定されていません');

        // アップロードされた画像ファイルが正しい場所に配置されていることを確認
        $blogImagePath = WWW_ROOT . 'files' . DS . 'blog' . DS . '1000' . DS . 'blog_posts' . DS . $blogResult['eye_catch'];
        $this->assertTrue(file_exists($blogImagePath), 'ブログ用のアイキャッチ画像ファイルが見つかりません');

        // アイキャッチ画像がPNG形式として有効か確認
        $imageInfo = getimagesize($blogImagePath);
        $this->assertNotFalse($imageInfo, 'アイキャッチ画像が有効な画像ではありません');
        $this->assertEquals(IMAGETYPE_PNG, $imageInfo[2], 'アイキャッチ画像がPNG形式ではありません');

        // テスト後のクリーンアップ
        if (file_exists($uploadedFile)) {
            unlink($uploadedFile);
        }
        if (file_exists($blogImagePath)) {
            (new BcFolder())->delete(WWW_ROOT . 'files' . DS . 'blog' . DS . '1');
        }
    }

}
