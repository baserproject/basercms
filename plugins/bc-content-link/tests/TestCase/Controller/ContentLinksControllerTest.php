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

namespace BcContentLink\Test\TestCase\Controller;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\MultiSiteScenario;
use BcContentLink\Controller\ContentLinksController;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Test\Factory\ContentLinkFactory;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * ContentLinksControllerTest
 * @property ContentLinksController $ContentLinksController
 */
class ContentLinksControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcContentLink.Factory/ContentLinks',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentLinksController = new ContentLinksController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinksController);
        parent::tearDown();
    }

    /**
     * Test initialize method
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->ContentLinksController->BcFrontContents);
    }

    /**
     * test view
     *
     * @return void
     */
    public function test_view(): void
    {
        //データ生成
        $this->loadFixtureScenario(MultiSiteScenario::class);
        ContentLinkFactory::make(['id' => 1, 'url' => '/test-new'])->persist();
        ContentFactory::make([
            'id' => 6,
            'name' => 'index',
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'entity_id' => 1,
            'url' => '/index',
            'site_id' => 1,
            'title' => 'test new link',
            'lft' => 1,
            'rght' => 2,
            'status' => true,
        ])->persist();

        //テスト実行成功場合、
        $this->get('/bc-content-link/content_links/view');
        $this->assertResponseSuccess();

        //戻る値を確認
        $rs = $this->_controller->viewBuilder()->getVars()['contentLink']->toArray();
        $this->assertEquals('/test-new', $rs['url']);
    }
}
