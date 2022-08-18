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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Service\SiteConfigsService;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;

class SiteConfigsControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites',
    ];

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
        Configure::clear();
        parent::tearDown();
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/site_configs/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());

        $site = new SiteConfigsService();
        $siteConfig = $site->get();

        $this->assertEquals($siteConfig["address"], $result->siteConfig->address);
        $this->assertEquals($siteConfig["email"], $result->siteConfig->email);
        $this->assertEquals($siteConfig["theme"], $result->siteConfig->theme);
        $this->assertEquals($siteConfig["editor_styles"], $result->siteConfig->editor_styles);
        $this->assertEquals($siteConfig["main_site_display_name"], $result->siteConfig->main_site_display_name);
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
        $data = [
            'email' => 'hoge@basercms.net'
        ];
        $this->post('/baser/api/baser-core/site_configs/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
    }

    /**
     * test check_sendmail
     */
    public function testCheckSendmail()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
