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

use BcContentLink\Test\Scenario\ContentLinksServiceScenario;
use BcContentLink\Service\ContentLinksService;
use BaserCore\TestSuite\BcTestCase;
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
        $this->ContentLinksService = new ContentLinksService();
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
     * @test get
     * @return void
     */
    public function test_get(): void
    {
        $this->loadFixtureScenario(ContentLinksServiceScenario::class);
        $data = $this->ContentLinksService->get(1);
        $this->assertNotEmpty($data);
        $data = $this->ContentLinksService->get(1, ['status' => 'publish']);
        $this->assertNotEmpty($data);
    }

    /**
     * @test create
     * @return void
     */
    public function test_create(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @test update
     * @return void
     */
    public function test_update(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
