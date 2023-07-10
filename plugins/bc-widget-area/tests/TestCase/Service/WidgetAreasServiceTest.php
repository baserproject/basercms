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

namespace BcWidgetArea\Test\TestCase\Service;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcWidgetArea\Service\WidgetAreasService;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use BcWidgetArea\Test\Scenario\WidgetAreasScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\ORM\TableRegistry;
use BaserCore\Error\BcException;
use BcWidgetArea\Model\Entity\WidgetArea;
use BcWidgetArea\Model\Table\WidgetAreasTable;
use Cake\Datasource\EntityInterface;

/**
 * WidgetAreasServiceTest
 * @property WidgetAreasService $WidgetAreasService
 */
class WidgetAreasServiceTest extends BcTestCase
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
    protected $fixtures = [
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/PermissionGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BcWidgetArea.Factory/WidgetAreas',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->WidgetAreasService = $this->getService(WidgetAreasServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test construct
     */
    public function test_construct()
    {
        $this->WidgetAreasService->__construct();
        $this->assertInstanceOf(WidgetAreasTable::class, $this->WidgetAreasService->WidgetAreas);
    }

    /**
     * test get
     */
    public function test_get()
    {
        //準備
        $this->loadFixtureScenario(WidgetAreasScenario::class);
        //正常系実行
        $result = $this->WidgetAreasService->get(1);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('ローカルナビゲーション', $result->widgets_array[0]['Widget2']['name']);
        //異常系実行
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->WidgetAreasService->get(99);

    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //準備
        $this->loadFixtureScenario(WidgetAreasScenario::class);

        //正常系実行: パラメータなし
        $result = $this->WidgetAreasService->getIndex()->all()->toArray();
        $this->assertCount(2, $result);
        $this->assertEquals(2, $result[1]->id);

        //正常系実行: limitパラメータを入れる
        $result = $this->WidgetAreasService->getIndex(['limit' => 1])->all()->toArray();
        $this->assertCount(1, $result);
        $this->assertEquals('標準サイドバー', $result[0]->name);

    }

    /**
     * test getNew
     */
    public function test_getNew()
    {

    }

    /**
     * test create
     */
    public function test_create()
    {
        //準備
        $postData = [
            'id' => 1,
            'name' => 'Nghiem',
            'widgets' => 'YTozOntpOjA7YToxOntzOjc6IldpZGdldDIiO2E6OTp7czoyOiJpZCI7czoxOiIyIjtzOjQ6InR5cGUiO3M6MzM6IuODreODvOOCq+ODq+ODiuODk+OCsuODvOOCt+ODp+ODsyI7czo3OiJlbGVtZW50IjtzOjEwOiJsb2NhbF9uYXZpIjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aToxO3M6NDoibmFtZSI7czozMzoi44Ot44O844Kr44Or44OK44OT44Ky44O844K344On44OzIjtzOjU6ImNhY2hlIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToxO2E6MTp7czo3OiJXaWRnZXQzIjthOjg6e3M6MjoiaWQiO3M6MToiMyI7czo0OiJ0eXBlIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6NzoiZWxlbWVudCI7czo2OiJzZWFyY2giO3M6NjoicGx1Z2luIjtzOjk6IkJhc2VyQ29yZSI7czo0OiJzb3J0IjtpOjI7czo0OiJuYW1lIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToyO2E6MTp7czo3OiJXaWRnZXQ0IjthOjk6e3M6MjoiaWQiO3M6MToiNCI7czo0OiJ0eXBlIjtzOjEyOiLjg4bjgq3jgrnjg4giO3M6NzoiZWxlbWVudCI7czo0OiJ0ZXh0IjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aTozO3M6NDoibmFtZSI7czo5OiLjg6rjg7Pjgq8iO3M6NDoidGV4dCI7czoyNzc6Ijx1bD48bGk+PGEgaHJlZj0iaHR0cHM6Ly9iYXNlcmNtcy5uZXQiIHRhcmdldD0iX2JsYW5rIj5iYXNlckNNU+OCquODleOCo+OCt+ODo+ODqzwvYT48L2xpPjwvdWw+PHA+PHNtYWxsPuOBk+OBrumDqOWIhuOBr+OAgeeuoeeQhueUu+mdouOBriBb6Kit5a6aXSDihpIgW+ODpuODvOODhuOCo+ODquODhuOCo10g4oaSIFvjgqbjgqPjgrjjgqfjg4Pjg4jjgqjjg6rjgqJdIOKGkiBb5qiZ5rqW44K144Kk44OJ44OQ44O8XSDjgojjgornt6jpm4bjgafjgY3jgb7jgZnjgII8L3NtYWxsPjwvcD4iO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319fQ=='
        ];
        //正常系実行
        $result = $this->WidgetAreasService->create($postData);
        $this->assertEquals('Nghiem', $result->name);
        $this->assertEquals('ローカルナビゲーション', $result->widgets_array[0]['Widget2']['name']);
        //データベースに保存したかを確認する
        $rs = $this->WidgetAreasService->get(1);
        $this->assertEquals(1, $rs->id);
        //異常系実行
        $postData = [
            'name' => '',
        ];
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name._empty: "ウィジェットエリア名を入力してください。")');
        $this->WidgetAreasService->create($postData);

    }

    /**
     * test update
     */
    public function test_update()
    {

    }

    /**
     * test delete
     */
    public function test_delete()
    {

    }

    /**
     * test getTitlesById
     */
    public function test_getTitlesById()
    {

    }

    /**
     * test batch
     */
    public function test_batch()
    {

    }

    /**
     * test updateWidget
     */
    public function test_updateWidget()
    {

    }

    /**
     * test updateSort
     */
    public function test_updateSort()
    {

    }

    /**
     * test deleteWidget
     */
    public function test_deleteWidget()
    {

    }

    /**
     * test getList
     */
    public function test_getList()
    {

    }

    /**
     * コントロールソース取得
     *
     * @param string $field
     */
    public function testGetControlSource()
    {
        $this->markTestIncomplete('このテストはまだ確認できていません。WidgetAreasTableより移行済');
        $result = $this->WidgetArea->getControlSource('id');
        $this->assertEquals([1 => 'ウィジェットエリア', 2 => 'ブログサイドバー'], $result, 'コントロールソースを取得できません');
    }

}
