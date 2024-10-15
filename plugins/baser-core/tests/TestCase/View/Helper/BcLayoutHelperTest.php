<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcLayoutHelper;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * class BcFormTableHelperTest
 * @property BcLayoutHelper $BcLayoutHelper
 */
class BcLayoutHelperTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcLayoutHelper = new BcLayoutHelper(new BcAdminAppView($this->loginAdmin($this->getRequest('/baser/admin'))));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test testDispatchContentsHeader
     */
    public function testDispatchContentsHeader()
    {
        //イベントをセット
        $this->entryEventToMock(self::EVENT_LAYER_HELPER, 'BcLayout.contentsHeader', function (Event $event) {
            $data = $event->getData();
            $this->assertEquals('Dashboard.Index', $data['id']);
            $event->setData('out', 'contentsHeader test');
        });
        //実装
        $rs = $this->BcLayoutHelper->dispatchContentsHeader();
        //戻り値を確認
        $this->assertEquals('contentsHeader test', $rs);
    }

    /**
     * test dispatchContentsFooter
     */
    public function testDispatchContentsFooter()
    {
        //イベントをセット
        $this->entryEventToMock(self::EVENT_LAYER_HELPER, 'BcLayout.contentsFooter', function (Event $event) {
            $data = $event->getData();
            $this->assertEquals('Dashboard.Index', $data['id']);
            $event->setData('out', 'contentsFooter test');
        });
        //実装
        $rs = $this->BcLayoutHelper->dispatchContentsFooter();
        //戻り値を確認
        $this->assertEquals('contentsFooter test', $rs);
    }
}
