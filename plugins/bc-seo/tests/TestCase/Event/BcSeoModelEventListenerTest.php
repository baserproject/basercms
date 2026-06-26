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

namespace BcSeo\Test\TestCase\Event;

use ArrayObject;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcSeo\Event\BcSeoModelEventListener;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcSeoModelEventListenerTest
 */
class BcSeoModelEventListenerTest extends BcTestCase
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
     * testBeforeFind
     */
    public function testBeforeFind()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');

        $query = $blogPostsTable->find();
        $listener = new BcSeoModelEventListener();
        $listener->beforeFind(new Event('beforeFind', $blogPostsTable), $query);
        // contain確認
        $this->assertArrayHasKey('SeoMetas', $query->getContain());
    }

    /**
     * testBeforeMarshal
     */
    public function testBeforeMarshal()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');

        $event = new Event('beforeMarshal', $blogPostsTable, ['options' => []]);
        $listener = new BcSeoModelEventListener();
        $listener->beforeMarshal($event, new ArrayObject());
        $options = $event->getData('options');
        // associated確認
        $this->assertContains('SeoMetas', $options['associated']);
    }

    /**
     * testAfterMarshal with direct seo_meta
     * entity->seo_meta直接アソシエーション時にバリデーションエラーでog_image_tmpが保持される
     */
    public function testAfterMarshalWithDirectSeoMeta()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
        $seoMetasTable = TableRegistry::getTableLocator()->get('BcSeo.SeoMetas');

        // SeoMetasTable に BcUpload がある場合のみテストを実行
        if (!$seoMetasTable->hasBehavior('BcUpload')) {
            $this->markTestSkipped('SeoMetasTableにBcUploadビヘイビアがありません');
            return;
        }

        // entity->seo_meta を直接セット
        $seoMeta = new Entity(['og_image' => 'old_og.jpg', 'og_image_tmp' => 'session_og_image.jpg', '_bc_upload_id' => 'test_direct_id']);
        $seoMeta->clean(); // cleanしてoriginalに設定
        $seoMeta->set('og_image', 'new_og.jpg'); // dirty（rollbackで元に戻るはず）

        $entity = new Entity(['title' => '', '_bc_upload_id' => 'test_direct_entity_id']);
        $entity->seo_meta = $seoMeta;
        $entity->setError('title', ['required']);

        $listener = new BcSeoModelEventListener();
        $event = new Event('afterMarshal', $blogPostsTable, ['options' => []]);
        $listener->afterMarshal($event, $entity, new ArrayObject(), new ArrayObject());

        // entity->seo_meta.og_image_tmpが保持されていることを確認
        $this->assertEquals(
            'session_og_image.jpg',
            $entity->seo_meta->get('og_image_tmp'),
            'entity->seo_meta経由でog_image_tmpが保持されるべきです'
        );
        // og_imageが元の値にロールバックされていることを確認
        $this->assertEquals(
            'old_og.jpg',
            $entity->seo_meta->get('og_image'),
            'rollbackFileによりog_imageが元の値に戻るべきです'
        );
    }

    /**
     * testAfterMarshal with content->seo_meta
     * entity->content->seo_meta経由でバリデーションエラー時にog_image_tmpが保持される
     */
    public function testAfterMarshalWithContentSeoMeta()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
        $seoMetasTable = TableRegistry::getTableLocator()->get('BcSeo.SeoMetas');

        // SeoMetasTable に BcUpload がある場合のみテストを実行
        if (!$seoMetasTable->hasBehavior('BcUpload')) {
            $this->markTestSkipped('SeoMetasTableにBcUploadビヘイビアがありません');
            return;
        }

        // entity->content->seo_meta 経由のアソシエーション
        $seoMeta = new Entity(['og_image_tmp' => 'session_content_og.jpg', '_bc_upload_id' => 'test_content_seo_id']);
        $content = new Entity();
        $content->seo_meta = $seoMeta;

        $entity = new Entity(['_bc_upload_id' => 'test_content_entity_id']);
        $entity->content = $content;
        $entity->setError('title', ['required']);

        $listener = new BcSeoModelEventListener();
        $event = new Event('afterMarshal', $blogPostsTable, ['options' => []]);
        $listener->afterMarshal($event, $entity, new ArrayObject(), new ArrayObject());

        // entity->seo_meta に同期されており、og_image_tmpが保持されていることを確認
        $this->assertInstanceOf(Entity::class, $entity->seo_meta, 'entity->seo_metaが同期されているべきです');
        $this->assertEquals(
            'session_content_og.jpg',
            $entity->seo_meta->get('og_image_tmp'),
            'entity->content->seo_meta経由でog_image_tmpが保持されるべきです'
        );
    }

    /**
     * testAfterMarshal no action on success
     * バリデーションエラーがない場合はafterMarshalは何もしない
     */
    public function testAfterMarshalNoActionOnSuccess()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])
            ->persist();
        $this->loginAdmin($this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1'));
        $blogPostsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');

        // エラーなしエンティティ + seo_meta with dirty og_image
        $seoMeta = new Entity(['og_image' => 'original_og.jpg']);
        $seoMeta->clean();
        $seoMeta->set('og_image', 'new_og.jpg'); // dirty状態

        $entity = new Entity(['title' => 'valid title']);
        $entity->seo_meta = $seoMeta;

        // エラーなし確認
        $this->assertFalse($entity->hasErrors());

        $listener = new BcSeoModelEventListener();
        $event = new Event('afterMarshal', $blogPostsTable, ['options' => []]);
        $listener->afterMarshal($event, $entity, new ArrayObject(), new ArrayObject());

        // エラーなしの場合はrollbackFileが呼ばれないため、og_imageが変更されたままであることを確認
        $this->assertEquals(
            'new_og.jpg',
            $entity->seo_meta->get('og_image'),
            'エラーなしの場合はrollbackFileが呼ばれず、og_imageが変更されたままであるべきです'
        );
    }
}
