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
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);

        //Getサービスをコル
        $rs = $this->EditorTemplatesService->get(1);

        //戻る値を確認
        $this->assertEquals(1, $rs->id);
        $this->assertEquals('画像（左）とテキスト', $rs->name);
    }

    /**
     * test getIndex
     */
    public function testGetIndex()
    {
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);

        //Getサービスをコル
        $rs = $this->EditorTemplatesService->getIndex();

        //戻る値を確認
        $this->assertEquals(3, $rs->count());
        $this->assertEquals(1, $rs->all()->toArray()[0]->id);
        $this->assertEquals('画像（左）とテキスト', $rs->all()->toArray()[0]->name);
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
        //正常テスト場合、
        $data['name'] = 'テスト追加';
        //新規追加メソッドを追加
        $rs = $this->EditorTemplatesService->create($data);
        //戻る値を確認
        $this->assertEquals('テスト追加', $rs->name);

        //異常テスト場合、
        $this->expectException('Cake\ORM\Exception\PersistenceFailedException');
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name.maxLength: "テンプレート名は50文字以内で入力してください。');
        $data['name'] = str_repeat('a', 51);
        $this->EditorTemplatesService->create($data);
    }

    /**
     * test update
     */
    public function testUpdate()
    {
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        //対象メソッドをコル
        $rs = $this->EditorTemplatesService->update($this->EditorTemplatesService->get(1), ['name' => 'edited']);
        //エディターテンプレートの名前が変更されるか確認
        $this->assertEquals('edited', $rs->name);

        //異常テスト場合、
        $this->expectException('Cake\ORM\Exception\PersistenceFailedException');
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name.maxLength: "テンプレート名は50文字以内で入力してください。');
        $data['name'] = str_repeat('a', 51);
        $this->EditorTemplatesService->update($this->EditorTemplatesService->get(1), $data);
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
