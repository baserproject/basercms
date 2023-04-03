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
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Controller\Api\ContentLinksController;
use BcContentLink\Test\Factory\ContentLinkFactory;
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
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Permissions',
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
        parent::tearDown();
    }

    /**
     * test view
     */
    public function test_view()
    {
        // データを生成
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

        // ログインしていないかつ公開ContentLinkIDをテスト場合、
        PermissionFactory::make()->allowGuest('/baser/api/admin/*')->persist();
        $this->get('/baser/api/bc-content-link/content_links/view/1.json');
        // レスポンスを確認
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('test link', $result->contentLink->content->title);

        //非公開ContentLinkIDをテスト場合、
        // APIを呼ぶ
        $this->get('/baser/api/bc-content-link/content_links/view/2.json');
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //ログインしていない状態では status パラメーターへへのアクセスを禁止するか確認
        $this->get('/baser/api/bc-content-link/content_links/view/1.json?status=publish');
        // レスポンスを確認
        $this->assertResponseCode(403);
    }

}
