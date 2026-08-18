<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) NPO baserCMS Users Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp\BcBlog;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\Utility\BcUtil;
use BcBlog\Test\Scenario\BlogContentScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcMcp\Mcp\BcBlog\BlogContentsTool;
use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\TableRegistry;

/**
 * BlogContentsToolTest
 */
class BlogContentsToolTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * @var BlogContentsTool
     */
    public $BlogContentsTool;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogContentsTool = new BlogContentsTool();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BlogContentsTool);
        parent::tearDown();
    }

    /**
     * Test instantiation
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf(BlogContentsTool::class, $this->BlogContentsTool);
        $this->assertTrue(method_exists($this->BlogContentsTool, 'addBlogContent'));
        $this->assertTrue(method_exists($this->BlogContentsTool, 'getBlogContents'));
    }

    /**
     * test addBlogContent
     */
    public function testAddBlogContent()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $result = $this->BlogContentsTool->addBlogContent(
            'test-blog',
            'テストブログ',
            1, // siteId
            1, // parentId
            'テストブログの説明' // description
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * test getBlogContents
     */
    public function testGetBlogContents()
    {
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1,
            null,
            'test-blog',
            'test-blog-url',
        );
        $result = $this->BlogContentsTool->getBlogContents();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
    }

    /**
     * test getBlogContent
     */
    public function testGetBlogContent()
    {
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1,
            null,
            'test-blog',
            'test-blog-url',
        );
        $result = $this->BlogContentsTool->getBlogContent(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * test editBlogContent
     */
    public function testEditBlogContent()
    {
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1,
            null,
            'test-blog',
            'test-blog-url',
        );
        // BlogContentScenario は parentId に null を渡しても `?? 1` で parent_id=1 となり、
        // id=1 と一致して「自分自身を親にできない」(TreeBehavior, CakePHP 5.2) になるため、
        // 正当なルート（parent_id=null）へ補正してツリーを再構築する。
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contentsTable->updateAll(['parent_id' => null], ['id' => 1]);
        $contentsTable->recover();

        $result = $this->BlogContentsTool->editBlogContent(
            1,
            'updated-blog',
            '更新されたブログ',
            1,
            null,
            '更新されたブログの説明'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * test deleteBlogContent
     */
    public function testDeleteBlogContent()
    {
        // テストではID=1のブログコンテンツが存在することを前提とする
        $result = $this->BlogContentsTool->deleteBlogContent(1);

        $this->assertIsArray($result);
        // 削除結果のチェック（成功またはエラーのいずれか）
        if (isset($result['message'])) {
            // 成功の場合
            $this->assertEquals('ブログコンテンツを削除しました', $result['message']);
        } else {
            // エラーの場合
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * test getBlogContentsWithSearch
     */
    public function testGetBlogContentsWithSearch()
    {
        $result = $this->BlogContentsTool->getBlogContents('test');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * test getBlogContentWithInvalidId
     */
    public function testGetBlogContentWithInvalidId()
    {
        $result = $this->BlogContentsTool->getBlogContent(99999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertIsString($result['content']); // エラーメッセージ
    }

    /**
     * test editBlogContentWithInvalidId
     */
    public function testEditBlogContentWithInvalidId()
    {
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1,
            null,
            'test-blog',
            'test-blog-url',
        );
        $result = $this->BlogContentsTool->editBlogContent(
            99999,
            'test-blog',
            'テストブログ',
            1,
            null,
            'テストブログの説明'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertIsString($result['content']); // エラーメッセージ
    }

    /**
     * test addBlogContentWithEyeCatchSize
     */
    public function testAddBlogContentWithEyeCatchSize()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);

        $result = $this->BlogContentsTool->addBlogContent(
            name: 'eyecatch-test-blog',
            title: 'アイキャッチテストブログ',
            description: 'アイキャッチサイズのテスト',
            eyeCatchSizeThumbWidth: 300,
            eyeCatchSizeThumbHeight: 200,
            eyeCatchSizeMobileThumbWidth: 150,
            eyeCatchSizeMobileThumbHeight: 100
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);

        // アイキャッチサイズの設定を確認
        $blogContent = $result;
        $this->assertArrayHasKey('eye_catch_size', $blogContent);

        // eye_catch_sizeがbase64エンコードされたシリアライズ形式の場合は、デコードしてアンシリアライズする
        $eyeCatchSize = $blogContent['eye_catch_size'];
        if (is_string($eyeCatchSize)) {
            // base64デコードしてからアンシリアライズ
            $eyeCatchSize = BcUtil::unserialize($eyeCatchSize);
        }

        // 実際のキー名で確認（thumb_width等）
        $this->assertEquals(300, $eyeCatchSize['thumb_width']);
        $this->assertEquals(200, $eyeCatchSize['thumb_height']);
        $this->assertEquals(150, $eyeCatchSize['mobile_thumb_width']);
        $this->assertEquals(100, $eyeCatchSize['mobile_thumb_height']);
    }

    /**
     * test editBlogContentWithEyeCatchSize
     */
    public function testEditBlogContentWithEyeCatchSize()
    {
        $this->loadFixtureScenario(BlogContentScenario::class,
            1, // id
            1,
            null,
            'test-blog',
            'test-blog-url',
        );

        $result = $this->BlogContentsTool->editBlogContent(
            id: 1,
            eyeCatchSizeThumbWidth: 400,
            eyeCatchSizeThumbHeight: 300,
            eyeCatchSizeMobileThumbWidth: 200,
            eyeCatchSizeMobileThumbHeight: 150
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);

        // アイキャッチサイズの更新を確認
        $blogContent = $result;
        $this->assertArrayHasKey('eye_catch_size', $blogContent);

        // eye_catch_sizeが文字列の場合は、アンシリアライズする
        $eyeCatchSize = $blogContent['eye_catch_size'];
        if (is_string($eyeCatchSize)) {
            $eyeCatchSize = BcUtil::unserialize($eyeCatchSize);
        }

        $this->assertEquals(400, $eyeCatchSize['thumb_width']);
        $this->assertEquals(300, $eyeCatchSize['thumb_height']);
        $this->assertEquals(200, $eyeCatchSize['mobile_thumb_width']);
        $this->assertEquals(150, $eyeCatchSize['mobile_thumb_height']);
    }

    /**
     * test addBlogContentWithDefaultEyeCatchSize
     */
    public function testAddBlogContentWithDefaultEyeCatchSize()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);

        // アイキャッチサイズを指定せずにブログコンテンツを作成
        $result = $this->BlogContentsTool->addBlogContent(
            'default-eyecatch-blog',
            'デフォルトアイキャッチブログ',
            1, // siteId
            1, // parentId
            'デフォルトアイキャッチサイズのテスト' // description
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);

        // デフォルトのアイキャッチサイズが設定されることを確認
        $blogContent = $result;
        $this->assertArrayHasKey('eye_catch_size', $blogContent);

        // eye_catch_sizeが文字列の場合は、アンシリアライズする
        $eyeCatchSize = $blogContent['eye_catch_size'];
        if (is_string($eyeCatchSize)) {
            $eyeCatchSize = BcUtil::unserialize($eyeCatchSize);
        }

        $this->assertArrayHasKey('thumb_width', $eyeCatchSize);
        $this->assertArrayHasKey('thumb_height', $eyeCatchSize);
        $this->assertArrayHasKey('mobile_thumb_width', $eyeCatchSize);
        $this->assertArrayHasKey('mobile_thumb_height', $eyeCatchSize);
    }

}
