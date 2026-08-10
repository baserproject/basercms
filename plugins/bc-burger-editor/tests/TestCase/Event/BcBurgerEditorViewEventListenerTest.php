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
use BcBurgerEditor\Event\BcBurgerEditorViewEventListener;
use Cake\Event\Event;
use Cake\View\View;

/**
 * BcBurgerEditorViewEventListenerTest
 */
class BcBurgerEditorViewEventListenerTest extends BcTestCase
{

    /**
     * @var BcBurgerEditorViewEventListener
     */
    public $BcBurgerEditorViewEventListener;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcBurgerEditorViewEventListener = new BcBurgerEditorViewEventListener();
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcBurgerEditorViewEventListener);
        parent::tearDown();
    }

    /**
     * test implementedEvents
     */
    public function test_implementedEvents()
    {
        $events = $this->BcBurgerEditorViewEventListener->implementedEvents();
        $this->assertArrayHasKey('View.beforeLayout', $events);
        $this->assertArrayHasKey('View.afterLayout', $events);
    }

    /**
     * test afterLayout
     *
     * Google Maps API が複数回読み込まれている場合、2つ目以降が除去される
     */
    public function test_afterLayout_removesDuplicatedGoogleMapsApi()
    {
        $script = '<script src="//maps.google.com/maps/api/js?key=test"></script>';
        $view = new View();
        $view->assign('content', '<div>' . $script . '</div><div>' . $script . '</div>');

        $this->BcBurgerEditorViewEventListener->afterLayout(new Event('View.afterLayout', $view));

        $result = $view->fetch('content');
        $this->assertSame(1, substr_count($result, 'maps.google.com/maps/api/js'), '重複した Google Maps API が除去されていません');
        $this->assertStringContainsString('<!-- delete google map api calling by BGE -->', $result);
    }

    /**
     * test afterLayout
     *
     * Google Maps API が1つだけの場合は書き換えられない
     */
    public function test_afterLayout_keepsSingleGoogleMapsApi()
    {
        $content = '<div><script src="//maps.google.com/maps/api/js?key=test"></script></div>';
        $view = new View();
        $view->assign('content', $content);

        $this->BcBurgerEditorViewEventListener->afterLayout(new Event('View.afterLayout', $view));

        $this->assertSame($content, $view->fetch('content'));
    }

    /**
     * test afterLayout
     *
     * Google Maps API 以外のスクリプトは除去されない
     */
    public function test_afterLayout_keepsOtherScripts()
    {
        $content = '<script src="/js/a.js"></script><script src="/js/b.js"></script>';
        $view = new View();
        $view->assign('content', $content);

        $this->BcBurgerEditorViewEventListener->afterLayout(new Event('View.afterLayout', $view));

        $this->assertSame($content, $view->fetch('content'));
    }

}
