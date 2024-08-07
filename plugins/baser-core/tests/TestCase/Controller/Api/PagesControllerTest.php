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

use BaserCore\Controller\Api\PagesController;
use BaserCore\Service\PagesService;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PagesScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\Api\PagesController Test Case
 */
class PagesControllerTest extends BcTestCase
{
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
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PagesScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
        $this->PagesService = new PagesService();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        PermissionFactory::make()->allowGuest('/baser/api/*')->persist();
        $this->get('/baser/api/baser-core/pages/index.json');
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/<section class="mainHeadline">/', $result->pages[0]->contents);
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/pages/view/2.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/<section class="mainHeadline">/', $result->page->contents);
    }

}
