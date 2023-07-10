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

    }

    /**
     * test update
     */
    public function test_update()
    {
        //準備
        $this->loadFixtureScenario(WidgetAreasScenario::class);

        //更新する前の確認
        $rs = $this->WidgetAreasService->get(1);
        $this->assertEquals('標準サイドバー', $rs->name);
        $this->assertEquals('ローカルナビゲーション', $rs->widgets_array[0]['Widget2']['name']);

        //正常系実行
        $postData = [
            'name' => 'Nghiem',
            'widgets' => 'YTo2OntpOjA7YToxOntzOjc6IldpZGdldDYiO2E6MTI6e3M6MjoiaWQiO3M6MToiNiI7czo0OiJ0eXBlIjtzOjI3OiLlubTliKXjgqLjg7zjgqvjgqTjg5bkuIDopqciO3M6NzoiZWxlbWVudCI7czoyMDoiYmxvZ195ZWFybHlfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjY6IkJjQmxvZyI7czo0OiJzb3J0IjtpOjY7czo0OiJuYW1lIjtzOjI3OiLlubTliKXjgqLjg7zjgqvjgqTjg5bkuIDopqciO3M6NToibGltaXQiO3M6MDoiIjtzOjEwOiJ2aWV3X2NvdW50IjtzOjE6IjEiO3M6MTE6InN0YXJ0X21vbnRoIjtzOjE6IjEiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6MTthOjE6e3M6NzoiV2lkZ2V0MSI7YTo5OntzOjI6ImlkIjtzOjE6IjEiO3M6NDoidHlwZSI7czoyNDoi44OW44Ot44Kw44Kr44Os44Oz44OA44O8IjtzOjc6ImVsZW1lbnQiO3M6MTM6ImJsb2dfY2FsZW5kYXIiO3M6NjoicGx1Z2luIjtzOjY6IkJjQmxvZyI7czo0OiJzb3J0IjtpOjE7czo0OiJuYW1lIjtzOjI0OiLjg5bjg63jgrDjgqvjg6zjg7Pjg4Djg7wiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIwIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6MjthOjE6e3M6NzoiV2lkZ2V0MiI7YToxMzp7czoyOiJpZCI7czoxOiIyIjtzOjQ6InR5cGUiO3M6MTg6IuOCq+ODhuOCtOODquS4gOimpyI7czo3OiJlbGVtZW50IjtzOjIyOiJibG9nX2NhdGVnb3J5X2FyY2hpdmVzIjtzOjY6InBsdWdpbiI7czo2OiJCY0Jsb2ciO3M6NDoic29ydCI7aToyO3M6NDoibmFtZSI7czoxODoi44Kr44OG44K044Oq5LiA6KanIjtzOjU6ImxpbWl0IjtzOjA6IiI7czoxMDoidmlld19jb3VudCI7czoxOiIxIjtzOjc6ImJ5X3llYXIiO3M6MToiMCI7czo1OiJkZXB0aCI7czoxOiIxIjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX1pOjM7YToxOntzOjc6IldpZGdldDMiO2E6MTA6e3M6MjoiaWQiO3M6MToiMyI7czo0OiJ0eXBlIjtzOjE1OiLmnIDov5Hjga7mipXnqL8iO3M6NzoiZWxlbWVudCI7czoxOToiYmxvZ19yZWNlbnRfZW50cmllcyI7czo2OiJwbHVnaW4iO3M6NjoiQmNCbG9nIjtzOjQ6InNvcnQiO2k6MztzOjQ6Im5hbWUiO3M6MTU6IuacgOi/keOBruaKleeovyI7czo1OiJjb3VudCI7czoxOiI1IjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX1pOjQ7YToxOntzOjc6IldpZGdldDQiO2E6MTA6e3M6MjoiaWQiO3M6MToiNCI7czo0OiJ0eXBlIjtzOjI0OiLjg5bjg63jgrDmipXnqL/ogIXkuIDopqciO3M6NzoiZWxlbWVudCI7czoyMDoiYmxvZ19hdXRob3JfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjY6IkJjQmxvZyI7czo0OiJzb3J0IjtpOjQ7czo0OiJuYW1lIjtzOjE1OiLmipXnqL/ogIXkuIDopqciO3M6MTA6InZpZXdfY291bnQiO3M6MToiMSI7czoxNToiYmxvZ19jb250ZW50X2lkIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aTo1O2E6MTp7czo3OiJXaWRnZXQ1IjthOjExOntzOjI6ImlkIjtzOjE6IjUiO3M6NDoidHlwZSI7czoyNzoi5pyI5Yil44Ki44O844Kr44Kk44OW5LiA6KanIjtzOjc6ImVsZW1lbnQiO3M6MjE6ImJsb2dfbW9udGhseV9hcmNoaXZlcyI7czo2OiJwbHVnaW4iO3M6NjoiQmNCbG9nIjtzOjQ6InNvcnQiO2k6NTtzOjQ6Im5hbWUiO3M6Mjc6IuaciOWIpeOCouODvOOCq+OCpOODluS4gOimpyI7czo1OiJsaW1pdCI7czoyOiIxMiI7czoxMDoidmlld19jb3VudCI7czoxOiIxIjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX19'
        ];
        $this->WidgetAreasService->update($rs, $postData);

        //更新する後の確認
        $rs = $this->WidgetAreasService->get(1);
        $this->assertEquals('Nghiem', $rs->name);
        $this->assertEquals('ブログカレンダー', $rs->widgets_array[0]['Widget1']['name']);

        //異常系実行
        $rs = $this->WidgetAreasService->get(1);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (name._empty: "ウィジェットエリア名を入力してください。")');
        $this->WidgetAreasService->update($rs, ['name' => '']);

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
