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

namespace BaserCore\Test\TestCase\View;

use BaserCore\Middleware\BcRequestFilterMiddleware;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\AppView;
use Cake\Event\Event;
use Cake\Event\EventManager;
use ReflectionClass;

/**
 * Class AppViewTest
 * @property AppView $AppView
 */
class AppViewTest extends BcTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // ヘルパ初期化時に $request->is('maintenance') 等の baserCMS 独自の検出器が必要なため、
        // ミドルウェアと同じ検出器を登録したリクエストを渡す
        $request = (new BcRequestFilterMiddleware())->addDetectors($this->getRequest());
        $this->AppView = new AppView($request);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->AppView);
        parent::tearDown();
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->AppView->BcPage);
        $this->assertNotEmpty($this->AppView->BcBaser);
    }

    /**
     * test getTemplateFileName For Event
     * @return void
     */
    public function test_getTemplateFileNameForEvent(): void
    {
        // コントローラー名をセット
        $ref = new ReflectionClass($this->AppView);
        $property = $ref->getProperty('name');
        $property->setValue($this->AppView, 'Users');

        // コントローラー名を指定しない場合
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'beforeGetTemplateFileName', function(Event $event) {
            $event->setData('name', 'ContentFolders/default');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getTemplateFileName', ['test']);
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/ContentFolders/default.php', $result);
        EventManager::instance()->off($listener);

        // コントローラー名が一致しない場合：呼び出されない
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'Sites.beforeGetTemplateFileName', function(Event $event) {
            $event->setData('name', 'ContentFolders/default');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getTemplateFileName', ['Users/login']);
        $this->assertNotEquals('/var/www/html/plugins/bc-front/templates/ContentFolders/default.php', $result);
        EventManager::instance()->off($listener);

        // コントローラー名が一致する場合：呼び出される
        $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'Users.beforeGetTemplateFileName', function(Event $event) {
            $event->setData('name', 'ContentFolders/default');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getTemplateFileName', ['Users/login']);
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/ContentFolders/default.php', $result);
    }

    /**
     * test _getLayoutFileName For Event
     * @return void
     */
    public function test_getLayoutFileNameForEvent(): void
    {
        // コントローラー名をセット
        $ref = new ReflectionClass($this->AppView);
        $property = $ref->getProperty('name');
        $property->setValue($this->AppView, 'Users');

        // コントローラー名を指定しない場合
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'beforeGetLayoutFileName', function(Event $event) {
            $event->setData('name', 'error');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getLayoutFileName');
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/layout/error.php', $result);
        EventManager::instance()->off($listener);

        // コントローラー名が一致しない場合：呼び出されない
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'Sites.beforeGetLayoutFileName', function(Event $event) {
            $event->setData('name', 'error');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getLayoutFileName');
        $this->assertNotEquals('/var/www/html/plugins/bc-front/templates/layout/error.php', $result);
        EventManager::instance()->off($listener);

        // コントローラー名が一致する場合：呼び出される
        $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'Users.beforeGetLayoutFileName', function(Event $event) {
            $event->setData('name', 'error');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getLayoutFileName');
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/layout/error.php', $result);
    }

    /**
     * test _getElementFileName For Event
     * @return void
     */
    public function test__getElementFileNameForEvent(): void
    {
        // コントローラー名をセット
        $ref = new ReflectionClass($this->AppView);
        $property = $ref->getProperty('name');
        $property->setValue($this->AppView, 'Users');

        // コントローラー名を指定しない場合
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'beforeGetElementFileName', function(Event $event) {
            $event->setData('name', 'header');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getElementFileName', ['test']);
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/element/header.php', $result);
        EventManager::instance()->off($listener);

        // コントローラー名が一致しない場合：呼び出されない
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'Sites.beforeGetElementFileName', function(Event $event) {
            $event->setData('name', 'header');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getElementFileName', ['test']);
        $this->assertNotEquals('/var/www/html/plugins/bc-front/templates/element/header.php', $result);
        EventManager::instance()->off($listener);

        // コントローラー名が一致する場合：呼び出される
        $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'Users.beforeGetElementFileName', function(Event $event) {
            $event->setData('name', 'header');
        });
        $result = $this->execPrivateMethod($this->AppView, '_getElementFileName', ['test']);
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/element/header.php', $result);
    }


    /**
     * test _getElementFileName - テンプレートパス外のファイルを element として解決しないこと
     *
     * element 名は DB 由来（ウィジェットの element、固定ページの page_template）の値が渡ることが
     * あるため、`..` でテンプレートパス外の PHP ファイルを指定しても読み込まれないことを確認する。
     */
    public function test__getElementFileName_rejectsPathTraversal(): void
    {
        // テンプレートパス配下の element は解決できる
        $result = $this->execPrivateMethod($this->AppView, '_getElementFileName', ['header']);
        $this->assertEquals('/var/www/html/plugins/bc-front/templates/element/header.php', $result);

        // テンプレートパス外（config/app.php）は拒否される
        $this->expectException('InvalidArgumentException');
        $this->execPrivateMethod($this->AppView, '_getElementFileName', ['../../../../config/app']);
    }
}
