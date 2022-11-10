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

use BcContentLink\Service\ContentLinksService;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Service\ContentLinksServiceInterface;
use BcContentLink\Test\Factory\ContentLinkFactory;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class ContentLinksServiceTest
 * @property ContentLinksService $ContentLinksService
 */
class ContentLinksServiceTest extends BcTestCase
{

    use BcContainerTrait;
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
