<?php

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Event\BcShortCodeEventListener;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use Cake\Event\Event;

class BcShortCodeEventListenerTest extends BcTestCase
{
    /**
     * @var BcShortCodeEventListener
     */
    protected $BcShortCodeEventListener;

    public function setUp(): void
    {
        parent::setUp();
        $this->BcShortCodeEventListener = new BcShortCodeEventListener();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->BcShortCodeEventListener->implementedEvents()));
    }

    /**
     * test afterRender
     */
    public function testAfterRender()
    {
        //準備
        $view = new BcFrontAppView($this->getRequest('/'));
        $view->loadHelper('BcBaser', ['className' => 'BaserCore.BcBaser']);
        $view->assign('content', '[BcBaser.getSitemap]');
        $event = new Event('afterRender', $view);
        //テストを実行
        $this->BcShortCodeEventListener->afterRender($event);
        //戻り値を確認
        $this->assertTextContains('<ul class="menu ul-level-1">', $view->fetch('content'));
    }

    /**
     * test _execShortCode
     */
    public function test_execShortCode()
    {
        //準備
        $view = new BcFrontAppView($this->getRequest('/'));
        $view->loadHelper('BcBaser', ['className' => 'BaserCore.BcBaser']);
        $view->assign('content', '[BcBaser.getSitemap]');
        //テストを実行
        $this->execPrivateMethod($this->BcShortCodeEventListener, '_execShortCode', [$view]);
        //戻り値を確認
        $this->assertTextContains('<ul class="menu ul-level-1">', $view->fetch('content'));
    }
}
