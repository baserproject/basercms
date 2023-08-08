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

namespace BcBlog\Test\TestCase\Controller\Admin;

use BcBlog\Controller\Admin\BlogAdminAppController;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;

/**
 * BlogAdminAppControllerTest Test Case
 *
 * @property  BlogAdminAppController $Controller
 */
class BlogAdminAppControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Controller = new BlogAdminAppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        // BlogAdminAppがセットされている事を確認
        $this->Controller->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcBlog.BlogAdminApp', $this->Controller->viewBuilder()->getClassName());
        // PreviewControllerを利用してBlogAdminAppがセットされない事を確認\
        $this->Controller->setRequest($this->Controller->getRequest()->withQueryParams(['preview' => 'default']));
        $this->Controller->viewBuilder()->setClassName('');
        $this->Controller->beforeRender(new Event('beforeRender'));
        $this->assertNotEquals('BcBlog.BlogAdminApp', $this->Controller->viewBuilder()->getClassName());
    }

}
