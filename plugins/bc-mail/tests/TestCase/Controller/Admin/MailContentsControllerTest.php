<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcMail\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Controller\Admin\MailContentsController;

class
MailContentsControllerTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $controller = new MailContentsController($this->getRequest());
        // コンポーネントが設定されたかどうかを確認する
        $this->assertNotEmpty($controller->BcAdminContents);
        // 設定されたconfigを確認する
        $this->assertEquals('mailContent', $controller->BcAdminContents->getConfig('entityVarName'));
        $this->assertTrue($controller->BcAdminContents->getConfig('useForm'));
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditMail
     */
    public function test_redirectEditMail()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditForm
     */
    public function test_redirectEditForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
