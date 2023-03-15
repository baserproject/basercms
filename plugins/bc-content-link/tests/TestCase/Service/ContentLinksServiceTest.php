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

namespace BcContentLink\Test\TestCase\Service;

use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BcContentLink\Test\Scenario\ContentLinksServiceScenario;
use BaserCore\Test\Factory\ContentFactory;
use BcContentLink\Service\ContentLinksService;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Service\ContentLinksServiceInterface;
use BcContentLink\Test\Factory\ContentLinkFactory;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ContentLinksServiceTest
 * @property ContentLinksService $ContentLinksService
 */
class ContentLinksServiceTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcContentLink.Factory/ContentLinks',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->ContentLinksService = $this->getService(ContentLinksServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinksService);
        parent::tearDown();
    }

    /**
     * @test construct
     * @return void
     */
    public function test__construct(): void
    {
        $this->assertTrue(isset($this->ContentLinksService->ContentLinks));
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        ContentLinkFactory::make(['id' => 1, 'url' => '/test-delete'])->persist();
        $this->assertTrue($this->ContentLinksService->delete(1));
    }
    /**
     * @test get
     * @return void
     */
    public function test_get(): void
    {
        $this->loadFixtureScenario(ContentLinksServiceScenario::class);
        $data = $this->ContentLinksService->get(1);
        $this->assertNotEmpty($data);
        $this->expectException(RecordNotFoundException::class);
        $this->ContentLinksService->get(1, ['status' => 'publish']);
    }

    /**
     * @test create
     * @return void
     */
    public function test_create(): void
    {
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $data = [
            'url' => '/test-create',
            'content' => [
                'plugin' => 'BcContentLink',
                'type' => 'ContentLink',
                'site_id' => 1,
                'title' => 'test create link',
                'lft' => 1,
                'rght' => 2,
                'name' => 'test create',
                'parent_id' => 1
            ]
        ];
        $rs = $this->ContentLinksService->create($data);
        $this->assertEquals($rs['url'], '/test-create');
        $this->assertEquals($rs['content']['title'], 'test create link');

        $data = [
            'url' => '/test-create'
        ];
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->expectExceptionMessage("関連するコンテンツがありません");
        $this->ContentLinksService->create($data);
    }

    /**
     * @test update
     * @return void
     */
    public function test_update(): void
    {
        ContentLinkFactory::make(['id' => 1, 'url' => '/test-new'])->persist();
        ContentFactory::make([
            'id' => 2,
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'site_id' => 1,
            'title' => 'test new link',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
        ])->persist();

        $contentLink = $this->ContentLinksService->get(1);
        $data = [
            'url' => '/test-edit',
            'content' => [
                'title' => 'test edit link',
            ]
        ];
        //実行成功
        $rs = $this->ContentLinksService->update($contentLink, $data);
        $this->assertEquals($rs['url'], '/test-edit');
        $this->assertEquals($rs['content']['title'], 'test edit link');

        //実行失敗
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->expectExceptionMessage("関連するコンテンツがありません");
        $this->ContentLinksService->update($contentLink, []);
    }
}
