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

namespace BcWidgetArea\Test\TestCase\Event;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\Event\BcWidgetAreaHelperEventListener;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcWidgetAreaHelperEventListenerTest
 *
 * @property  BcWidgetAreaHelperEventListener $BcWidgetAreaHelperEventListener
 */
class BcWidgetAreaHelperEventListenerTest extends BcTestCase
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
        $this->BcWidgetAreaHelperEventListener = new BcWidgetAreaHelperEventListener();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcWidgetAreaControllerEventListener);
        parent::tearDown();
    }

    /**
     * test formAfterForm
     */
    public function testFormAfterForm()
    {
        //準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $event = new Event('formAfterForm', new BcAdminAppView($request));
        $event->setData('id', 'SiteConfigFormForm');

        //formAfterFormをコール
        $this->BcWidgetAreaHelperEventListener->formAfterForm($event);

        //fieldsのデータがセットできるか確認すること
        $fields = $event->getData('fields');
        $this->assertEquals('<label for="widget-area">標準ウィジェットエリア</label>', $fields[0]['title']);
        $this->assertStringContainsString('ウィジェットエリアは', $fields[0]['input']);
    }
}
