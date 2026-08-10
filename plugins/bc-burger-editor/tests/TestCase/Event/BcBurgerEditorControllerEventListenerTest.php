<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\Event;

use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Event\BcBurgerEditorControllerEventListener;
use Cake\Controller\Controller;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Routing\Router;

/**
 * BcBurgerEditorControllerEventListenerTest
 */
class BcBurgerEditorControllerEventListenerTest extends BcTestCase
{

    /**
     * @var BcBurgerEditorControllerEventListener
     */
    public $BcBurgerEditorControllerEventListener;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcBurgerEditorControllerEventListener = new BcBurgerEditorControllerEventListener();
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcBurgerEditorControllerEventListener);
        parent::tearDown();
    }

    /**
     * test イベントリスナーのクラス名がコアプラグインの規約に沿っている
     *
     * BcEvent::registerPluginEvent() は「プラグイン名＋レイヤー名＋EventListener」という
     * ファイル名でのみリスナーを探索するため、名前がずれると警告もなく登録されなくなる
     *
     * @param string $layer
     * @dataProvider eventListenerLayerDataProvider
     */
    public function test_eventListenerClassName($layer)
    {
        $path = Plugin::path('BcBurgerEditor') . 'src' . DS . 'Event' . DS . 'BcBurgerEditor' . $layer . 'EventListener.php';
        $this->assertFileExists($path, "BcEvent::registerPluginEvent() が探索するファイル名と一致していません");
        $this->assertTrue(class_exists('\BcBurgerEditor\Event\BcBurgerEditor' . $layer . 'EventListener'));
    }

    public static function eventListenerLayerDataProvider()
    {
        return [
            ['Controller'],
            ['View'],
        ];
    }

    /**
     * test implementedEvents
     */
    public function test_implementedEvents()
    {
        $this->assertArrayHasKey('Controller.initialize', $this->BcBurgerEditorControllerEventListener->implementedEvents());
    }

    /**
     * test initialize
     *
     * 対象外のアクションでもヘルパーは追加される
     */
    public function test_initialize_addsHelper()
    {
        $request = $this->getRequest('/baser/admin/baser-core/users/index');
        Router::setRequest($request);
        $controller = new Controller($request);
        $event = new Event('Controller.initialize', $controller);

        $this->BcBurgerEditorControllerEventListener->initialize($event);

        $helpers = $controller->viewBuilder()->getHelpers();
        $this->assertArrayHasKey('BurgerEditor', $helpers);
        $this->assertSame('BcBurgerEditor.BurgerEditor', $helpers['BurgerEditor']['className']);
    }

}
