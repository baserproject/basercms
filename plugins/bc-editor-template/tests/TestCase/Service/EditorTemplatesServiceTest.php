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
use BcEditorTemplate\Test\Scenario\EditorTemplatesScenario;
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
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        //対象メソッドをコル
        $rs = $this->EditorTemplatesService->getList();
        //期待値
        $expect = [
            1 => '画像（左）とテキスト',
            2 => '画像（右）とテキスト',
            3 => 'テキスト２段組',
        ];
        //期待値を戻るかどうか確認
        $this->assertEquals($expect, $rs);
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
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        //対象メソッドをコル
        $rs = $this->EditorTemplatesService->delete(1);
        //戻り値を確認
        $this->assertTrue($rs);

        //削除したエディターテンプレートが存在しないか確認すること
        $this->expectException("Cake\Datasource\Exception\RecordNotFoundException");
        $this->EditorTemplatesService->get(1);
    }

}
