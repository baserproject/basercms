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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\AppView;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcFormTableHelper;
use BcAppView;
use Cake\Event\Event;

/**
 * class BcFormTableHelperTest
 * @property BcFormTableHelper $BcFormTable
 */
class BcFormTableHelperTest extends BcTestCase
{

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $View = new BcAdminAppView();
        $this->BcFormTable = new BcFormTableHelper($View);
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test dispatchBefore
     */
    public function testDispatchBefore()
    {
        $view = $this->BcFormTable->getView();
        $view->BcAdminForm->setId('test1');
        $this->entryEventToMock(self::EVENT_LAYER_HELPER, 'BcFormTable.before', function(Event $event) {
            $this->assertEquals('test1', $event->getData('id'));
            $event->setData('out', 'test2');
        });
        $this->assertEquals('test2', $this->BcFormTable->dispatchBefore());
    }

    /**
     * test dispatchAfter
     */
    public function testDispatchAfter()
    {
        $view = $this->BcFormTable->getView();
        $view->BcAdminForm->setId('test1');
        $this->entryEventToMock(self::EVENT_LAYER_HELPER, 'BcFormTable.after', function(Event $event) {
            $this->assertEquals('test1', $event->getData('id'));
            $event->setData('out', 'test2');
        });
        $this->assertEquals('test2', $this->BcFormTable->dispatchAfter());
    }
}
