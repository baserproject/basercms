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

    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {

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
