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
     * test add
     */
    public function test_add()
    {
        $data = [
            'url' => '/test-add',
            'content' => [
                'plugin' => 'BcContentLink',
                'type' => 'ContentLink',
                'site_id' => 1,
                'title' => 'test add link',
                'lft' => 1,
                'rght' => 2,
                'entity_id' => 1,
            ]
        ];
        $this->post('/baser/api/bc-content-link/content_links/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('リンク「test add link」を追加しました。', $result->message);
        $this->assertEquals('BcContentLink', $result->content->plugin);
        $this->assertEquals('/test-add', $result->contentLink->url);

        $data = [
            'url' => '/test-add',
        ];
        $this->post('/baser/api/bc-content-link/content_links/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('関連するコンテンツがありません', $result->errors->content->_required);
    }
    /**
     * test edit
     */
    public function test_edit()
    {
        ContentLinkFactory::make(['id' => 1, 'url' => '/test'])->persist();
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

        $data = [
            'id' => 1,
            'url' => '/test-edit',
            'content' => [
                "title" => "更新 BcContentLink",
            ]
        ];
        $this->post('/baser/api/bc-content-link/content_links/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('コンテンツリンク: 「更新 BcContentLink」を更新しました。', $result->message);
        $this->assertEquals('/test-edit', $result->contentLink->url);

        $this->post('/baser/api/bc-content-link/content_links/edit/10.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('Record not found in table "content_links"', $result->errors);

        $data = [
            'id' => 1,
            'url' => '/test-edit',
            'content' => [
                "title" => "更新 BcContentLink",
                "parent_id" => "level"
            ]
        ];
        $this->post('/baser/api/bc-content-link/content_links/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('Cannot convert value of type `string` to integer', $result->errors);
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

    /**
     * test view
     */
    public function test_view()
    {
        ContentLinkFactory::make(['id' => 1, 'url' => '/test-delete'])->persist();
        ContentFactory::make([
            'id' => 1,
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'site_id' => 1,
            'title' => 'test link',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'status' => true
        ])->persist();
        ContentLinkFactory::make(['id' => 2, 'url' => '/test-delete'])->persist();
        ContentFactory::make([
            'id' => 2,
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'site_id' => 1,
            'title' => 'test link',
            'lft' => 3,
            'rght' => 4,
            'entity_id' => 2,
            'status' => false
        ])->persist();

        $this->get('/baser/api/bc-content-link/content_links/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('test link', $result->contentLink->content->title);

        $this->get('/baser/api/bc-content-link/content_links/view/2.json?token=' . $this->accessToken);
        $this->assertResponseCode(401);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('Record not found in table "content_links"', $result->message);
    }
}
