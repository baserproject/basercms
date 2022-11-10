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

namespace BcContentLink\Test\TestCase\Controller\Api;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Controller\Api\ContentLinksController;
use BcContentLink\Test\Factory\ContentLinkFactory;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ContentLinksControllerTest
 * @property ContentLinksController $ContentLinksController
 */
class ContentLinksControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BcContentLink.Factory/ContentLinks',
        'plugin.BaserCore.Factory/Contents',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin();
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        ContentLinkFactory::make(['id' => 1, 'url' => '/test-delete'])->persist();
        ContentFactory::make([
            'id' => 2,
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'site_id' => 1,
            'title' => 'test delete link',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
        ])->persist();

        $this->post('/baser/api/bc-content-link/content_links/delete/1.json?token=' . $this->accessToken);

        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('test delete link', $result->contentLink->content->title);
        $this->assertEquals('コンテンツリンク: test delete link を削除しました。', $result->message);

        $this->get('/baser/api/bc-content-link/content_links/delete/1.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/bc-content-link/content_links/delete/10000.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('Record not found in table "content_links"', $result->message);
    }

}
