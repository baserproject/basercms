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
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Event\BcCustomContentControllerEventListener;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
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

        $this->BcAuthenticationEventListener = new BcCustomContentControllerEventListener();
        foreach ($this->BcAuthenticationEventListener->implementedEvents() as $key => $event) {
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
        $this->BcAuthenticationEventListener = null;
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
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        $listener = $this->getMockBuilder(BcCustomContentControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersStartup'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.startup' => ['callable' => 'usersStartup']]);

        $this->eventManager
            ->on($listener)
            ->on($this->BcAuthenticationEventListener)
            ->dispatch(new Event('Controller.startup', $this->UsersController, []));

        //メーニューにカスタムタイトルがあるか確認
        $menu = Configure::read('BcApp.adminNavigation.Contents');
        $this->assertEquals('サービスタイトル', $menu['CustomContent1']['title']);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }
}
