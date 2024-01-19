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

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Event\Event;
use Cake\Controller\Controller;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Event\BcControllerEventListener;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcControllerEventListenerTest
 *
 * @property  BcControllerEventListener $BcControllerEventListener
 */
class BcControllerEventListenerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcControllerEventListener = new BcControllerEventListener();
        // $BcAdminAppView = new BcAdminAppView($this->getRequest('/baser/admin'));
        // $this->BcAdminAppView = $BcAdminAppView->setPlugin("BcAdminThird");
        // $this->Content = $this->getTableLocator()->get('BaserCore.Contents')->get(1);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcControllerEventListener);
        parent::tearDown();
    }

    /**
     * 管理システムの現在のサイトをセットする
     */
    public function testSetAdminCurrentSite()
    {
        $controller = new Controller($this->getRequest('baser/admin'));
        $site_id = 1;
        $this->assertTrue($this->BcControllerEventListener->setAdminCurrentSite($controller, $site_id));
        $this->assertEquals($site_id, $controller->getRequest()->getAttribute('currentSite')->id);
    }

    /**
     * コントローラーにヘルパーを追加する
     * @dataProvider addHelperDataProvider
     */
    public function testAddHelper($helpers, $expected)
    {
        $controller = new Controller($this->getRequest());
        $this->BcControllerEventListener->addHelper($controller, $helpers);
        $this->assertEquals($expected, $controller->viewBuilder()->getHelpers());
    }

    public static function addHelperDataProvider()
    {
        return [
            ['BcBaser', ['BcBaser' => []]],
            [['BcBaser', 'BcTime'], ['BcBaser' => [], 'BcTime' => []]]
        ];
    }

}
