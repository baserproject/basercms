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

namespace BcContentLink\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcContentLink\Controller\Admin\ContentLinksController;
use BcContentLink\Service\ContentLinksServiceInterface;
use BcContentLink\Test\Factory\ContentLinkFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcContentLinkTest
 *
 * @property ContentLinksController $ContentLinksController
 */
class ContentLinksControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
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
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->ContentLinksController = new ContentLinksController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinksController);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->ContentLinksController->BcAdminContents);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        ContentLinkFactory::make(['id' => 1, 'url' => '/test'])->persist();
        ContentFactory::make([
            'id' => 1,
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'site_id' => 1,
            'title' => 'test delete link',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
        ])->persist();

        //実行成功場合、
        $data = [
            'id' => 1,
            'url' => '/test-edit',
            'content' => [
                "title" => "更新 BcContentLink",
            ]
        ];
        $this->post('/baser/admin/bc-content-link/content_links/edit/1', $data);
        //リダイレクトを確認
        $this->assertResponseCode(302);
        $this->assertRedirect('/baser/admin/bc-content-link/content_links/edit/1');
        //更新したデータを確認
        $contentLinkService = $this->getService(ContentLinksServiceInterface::class);
        $contentLink = $contentLinkService->get(1);
        $this->assertEquals('/test-edit', $contentLink['url']);
        $this->assertEquals('更新 BcContentLink', $contentLink['content']['title']);
        //メッセージを確認
        $this->assertFlashMessage('リンク「更新 BcContentLink」を更新しました。');

        //実行失敗場合、
        $data = [
            'url' => '/test-edit-2'
        ];
        $this->post('/baser/admin/bc-content-link/content_links/edit/1', $data);
        //リダイレクトしないを確認
        $this->assertResponseCode(200);
        //エラーメッセージを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(['content' => ['_required' => "関連するコンテンツがありません"]], $vars['contentLink']->getErrors());
    }
}
