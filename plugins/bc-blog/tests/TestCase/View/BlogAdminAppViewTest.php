<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\View;

use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\View\BlogAdminAppView;
use BaserCore\TestSuite\BcTestCase;

/**
 * BcContents helper library.
 *
 * @property BlogAdminAppView $BlogAdminAppView
 */
class BlogAdminAppViewTest extends BcTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogAdminAppView = new BlogAdminAppView($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogAdminAppView);
        parent::tearDown();
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->BlogAdminAppView->initialize();
        $this->assertNotEmpty($this->BlogAdminAppView->Blog);
    }

    /**
     * ブログ記事フォームで草稿欄のエラーを表示する
     */
    public function testRenderBlogPostFormDisplaysDraftError(): void
    {
        $request = $this->getRequest('/baser/admin/bc-blog/blog_posts/edit/1/1')
            ->withParam('plugin', 'BcBlog')
            ->withParam('prefix', 'Admin')
            ->withParam('controller', 'BlogPosts')
            ->withAttribute('formTokenData', ['dummy']);
        $this->BlogAdminAppView->setRequest($request);
        $this->BlogAdminAppView->setTheme('BcAdminThird');

        $post = BlogPostFactory::make([
            'blog_content_id' => 1,
            'title' => 'test',
            'name' => 'test',
            'detail' => '',
            'detail_draft' => '<?php echo $test; ?>',
            'status' => false,
            'posted' => null,
            'publish_begin' => null,
            'publish_end' => null,
        ])->getEntity();
        $post->setError('detail_draft', ['containsScript' => '草稿欄でスクリプトの入力は許可されていません。']);

        $blogContent = BlogContentFactory::make([
            'id' => 1,
            'use_content' => true,
            'tag_use' => false,
        ])->getEntity();

        $this->BlogAdminAppView->set(compact('post', 'blogContent'));
        $this->BlogAdminAppView->set([
            'editor' => 'BaserCore.BcCkeditor',
            'editorOptions' => [],
            'editorEnterBr' => false,
            'users' => [],
            'categories' => [],
            'hasNewCategoryAddablePermission' => false,
            'hasNewTagAddablePermission' => false,
            'fullUrl' => 'https://localhost'
        ]);
        $this->BlogAdminAppView->BcAdminForm->BcUpload->setTable('BcBlog.BlogPosts');
        $this->BlogAdminAppView->BcAdminForm->create($post);

        $result = $this->BlogAdminAppView->element('BlogPosts/form');

        $this->assertStringContainsString('草稿欄でスクリプトの入力は許可されていません。', $result);
    }

}
