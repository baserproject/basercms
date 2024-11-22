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

namespace BcCustomContent\Test\TestCase\Event;

use BaserCore\Controller\Admin\UsersController;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Event\BcCustomContentControllerEventListener;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomContentFactory;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcCustomContentControllerEventListenerTest
 *
 * @property  BcCustomContentControllerEventListener $BcCustomContentControllerEventListener
 */
class BcCustomContentControllerEventListenerTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * @var UsersController
     */
    public $UsersController;

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->eventManager = EventManager::instance();

        $this->BcCustomContentControllerEventListener = new BcCustomContentControllerEventListener();
        foreach ($this->BcCustomContentControllerEventListener->implementedEvents() as $key => $event) {
            $this->eventManager->off($key);
        }
        $this->UsersController = new UsersController($this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/')));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->BcCustomContentControllerEventListener = null;
        parent::tearDown();
    }

    /**
     * test startup
     */
    public function testStartupAnSetAdminMenu()
    {
        //データを生成
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create(['id' => 1, 'name' => 'recruit_categories']);
        CustomContentFactory::make(['id' => 1, 'custom_table_id' => 1])->persist();
        ContentFactory::make([
            'plugin' => 'BcCustomContent',
            'type' => 'CustomContent',
            'site_id' => 1,
            'title' => 'サービスタイトル',
            'entity_id' => 1,
        ])->persist();

        $listener = $this->getMockBuilder(BcCustomContentControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersStartup'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.startup' => ['callable' => 'usersStartup']]);

        $this->eventManager
            ->on($listener)
            ->on($this->BcCustomContentControllerEventListener)
            ->dispatch(new Event('Controller.startup', $this->UsersController, []));

        //メーニューにカスタムタイトルがあるか確認
        $menu = Configure::read('BcApp.adminNavigation.Contents');
        $this->assertEquals('サービスタイトル', $menu['CustomContent1']['title']);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }
}
