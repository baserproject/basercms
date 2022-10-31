<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Controller
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogContentsController;

/**
 * Class BlogContentsControllerTest
 *
 * @package Blog.Test.Case.Controller
 * @property  BlogContentsController $BlogContentsController
 */
class BlogContentsControllerTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown():void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $controller = new BlogContentsController($this->getRequest());
        $this->assertNotEmpty($controller->BcAdminContents);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditLayout
     */
    public function test_redirectEditLayout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditBlog
     */
    public function test_redirectEditBlog()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
