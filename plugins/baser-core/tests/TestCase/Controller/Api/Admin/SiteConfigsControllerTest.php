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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use BaserCore\Service\SiteConfigsService;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class SiteConfigsControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
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
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/admin/baser-core/site_configs/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());

        $site = new SiteConfigsService();
        $siteConfig = $site->get();

        $this->assertEquals($siteConfig["address"], $result->siteConfig->address);
        $this->assertEquals($siteConfig["email"], $result->siteConfig->email);
        $this->assertEquals($siteConfig["theme"], $result->siteConfig->theme);
        $this->assertEquals($siteConfig["editor_styles"], $result->siteConfig->editor_styles);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = ['email' => 'hoge@basercms.net'];
        $this->post('/baser/api/admin/baser-core/site_configs/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();

        //エラーを発生した場合、
        $this->post('/baser/api/admin/baser-core/site_configs/edit/1.json?token=' . $this->accessToken, ['email' => '']);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('管理者メールアドレスを入力してください。', $result->errors->email->_empty);
    }

    /**
     * test check_sendmail
     */
    public function testCheckSendmail()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
