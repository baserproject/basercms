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

namespace BaserCore\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcViewEventDispatcher
 *
 * ビューイベントディスパッチャ
 *
 * beforeRender 等の、CakePHPのビュー向け標準イベントについて、
 * コントローラーごとにイベントをディスパッチする。
 * bootstrap で、attach される。
 *
 * 《イベント名の命名規則》
 * View.ControllerName.eventName
 */
class BcViewEventDispatcher implements EventListenerInterface
{

    /**
     * implementedEvents
     *
     * @return string[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function implementedEvents(): array
    {
        return [
            'View.beforeRenderFile' => 'beforeRenderFile',
            'View.afterRenderFile' => 'afterRenderFile',
            'View.beforeRender' => 'beforeRender',
            'View.afterRender' => 'afterRender',
            'View.beforeLayout' => 'beforeLayout',
            'View.afterLayout' => 'afterLayout'
        ];
    }

    /**
     * beforeRenderFile
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRenderFile(Event $event)
    {
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('beforeRenderFile', $event->getData());
        }
    }

    /**
     * afterRenderFile
     *
     * @param Event $event
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterRenderFile(Event $event): string
    {
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return $event->getData(1);
            }
            $currentEvent = $event->getSubject()->dispatchLayerEvent('afterRenderFile', $event->getData());
            if ($currentEvent) {
                return $currentEvent->getData(1);
            }
        }
        return $event->getData(1);
    }

    /**
     * beforeRender
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(Event $event)
    {
        if ($event->getSubject()->getName() != 'Error' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('beforeRender', $event->getData());
        }
    }

    /**
     * afterRender
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterRender(Event $event)
    {
        if ($event->getSubject()->getName() != 'Error' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('afterRender', $event->getData());
        }
    }

    /**
     * beforeLayout
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeLayout(Event $event)
    {
        if ($event->getSubject()->getName() != 'Error' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('beforeLayout', $event->getData());
        }
    }

    /**
     * afterLayout
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterLayout(Event $event)
    {
        if ($event->getSubject()->getName() != 'Error' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('afterLayout', $event->getData());
        }
    }

}
