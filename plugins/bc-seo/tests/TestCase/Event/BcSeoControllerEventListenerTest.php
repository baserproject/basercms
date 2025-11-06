<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\Test\TestCase\Event;

use BaserCore\TestSuite\BcTestCase;
use BcSeo\Event\BcSeoControllerEventListener;
use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * BcSeoControllerEventListenerTest
 */
class BcSeoControllerEventListenerTest extends BcTestCase
{
    /**
     * testBeforeRender
     */
    public function testBeforeRender()
    {
        $controller = new Controller($this->getRequest());
        $event = new Event('beforeRender', $controller);
        $listener = new BcSeoControllerEventListener();
        $listener->beforeRender($event);
        // ヘルパー確認
        $helpers = $controller->viewBuilder()->getHelpers();
        $this->assertArrayHasKey('Seo', $helpers);
    }
}
