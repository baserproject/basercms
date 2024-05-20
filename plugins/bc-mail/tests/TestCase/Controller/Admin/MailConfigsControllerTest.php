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
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailConfigsControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    /**
     * set up
     *
     * @return void
     */
    public function tearDown(): void
    {
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

    /**
     * test index
     */
    public function testIndex()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //request is get
        $this->get('/baser/admin/bc-mail/mail_configs/index');
        $this->assertResponseOk();

        //request is post
        $this->post('/baser/admin/bc-mail/mail_configs/index', ['name_add' => 'test']);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('メールプラグイン設定を保存しました。');
        $this->assertRedirect('/baser/admin/bc-mail/mail_configs/index');
    }
}
