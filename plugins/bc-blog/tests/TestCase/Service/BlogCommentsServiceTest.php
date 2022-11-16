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

namespace BcBlog\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Service\BlogCommentsService;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogCommentsServiceTest
 * @property BlogCommentsService $BlogCommentsService
 */
class BlogCommentsServiceTest extends BcTestCase
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
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogComments',
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
        $this->BlogCommentsService = new BlogCommentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getIndex
     */
    public function testGetIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test get
     */
    public function testGet()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test publish
     */
    public function testPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test unpublish
     */
    public function testUnpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test batch
     */
    public function testBatch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
