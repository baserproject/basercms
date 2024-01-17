<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */
namespace BcMail\Test\TestCase\Controller\Admin;
use BaserCore\TestSuite\BcTestCase;

class MailConfigsControllerTest extends BcTestCase
{
    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
//        $this->MailConfigs = new MailConfigsController(new CakeRequest(null, false), new CakeResponse());
//
//        $this->Case = $this->getMockForAbstractClass('ControllerTestCase');

        parent::setUp();
    }

    /**
     * set up
     *
     * @return void
     */
    public function tearDown(): void
    {
//        unset($this->MailConfigs);
//        unset($this->Case);
        parent::tearDown();
    }

    /**
     * [ADMIN] メールフォーム設定
     *
     * @param array $data requestのdata
     * @dataProvider admin_formDataProvider
     */
    public function test_index($data, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        session_id('baser');
        $this->Case->testAction("admin/mail/mail_configs/form", [
            'method' => 'post', 'data' => $data
        ]);

        if (!empty($data)) {
            $flash = CakeSession::read('Message.flash');
            $this->assertEquals($flash['message'], 'メールフォーム設定を保存しました。');
        }

        $url = $this->Case->headers['Location'];
        $this->assertMatchesRegularExpression('/' . $expected . '/', $url);
    }

    public static function admin_formDataProvider()
    {
        return [
            [[], '\/admin\/users\/login'],
            [[
                "MailConfig" => [
                    "site_name" => "test"
                ]
            ], '\/admin\/mail\/mail_configs\/form']
        ];
    }
}
