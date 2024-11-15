<?php

namespace BcBlog\Test\TestCase\Mailer;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Mailer\BlogCommentMailer;

class BlogCommentMailerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        SiteConfigFactory::make(['name' => 'email', 'value' => 'basertest@example.com'])->persist();
        $this->BlogCommentMailer = new BlogCommentMailer();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test sendCommentToAdmin
     */
    public function testSendCommentToAdmin()
    {
        $this->BlogCommentMailer->sendCommentToAdmin('test', ['test' => 'test']);
        $this->assertEquals('BcBlog.blog_comment_admin', $this->BlogCommentMailer->viewBuilder()->getTemplate());
        $this->assertEquals(['test' => 'test'], $this->BlogCommentMailer->viewBuilder()->getVars());
    }
}
