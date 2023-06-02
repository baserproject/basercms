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

namespace BcEditorTemplate\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcEditorTemplate\Service\EditorTemplatesService;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * EditorTemplatesServiceTest
 * @property EditorTemplatesService $EditorTemplatesService
 */
class EditorTemplatesServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BcEditorTemplate.Factory/EditorTemplates',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->EditorTemplatesService = new EditorTemplatesService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->EditorTemplatesService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function testConstruct()
    {
        $this->assertTrue(isset($this->EditorTemplatesService->EditorTemplates));
    }

    /**
     * test getNew
     */
    public function testGetNew()
    {
        $this->assertEquals($this->EditorTemplatesService->getNew()->toArray(), []);
    }

    /**
     * test get
     */
    public function testGet()
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
     * test getList
     */
    public function testGetList()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function testCreate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test update
     */
    public function testUpdate()
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

}
