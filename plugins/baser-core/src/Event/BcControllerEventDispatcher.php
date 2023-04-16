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
 * Class BcControllerEventDispatcher
 *
 * コントローラーイベントディスパッチャ
 *
 * beforeRender 等の、CakePHPのコントローラー向け標準イベントについて、
 * コントローラーごとにイベントをディスパッチする。
 * bootstrap で attach される。
 *
 * 《イベント名の命名規則》
 * Controller.ControllerName.eventName
 */
class BcControllerEventDispatcher implements EventListenerInterface
{

    /**
     * implementedEvents
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function implementedEvents(): array
    {
        return [
            'Controller.initialize' => ['callable' => 'initialize'],
            'Controller.startup' => ['callable' => 'startup'],
            'Controller.beforeRender' => ['callable' => 'beforeRender'],
            'Controller.beforeRedirect' => ['callable' => 'beforeRedirect'],
            'Controller.shutdown' => ['callable' => 'shutdown'],
        ];
    }

    /**
     * initialize
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(Event $event)
    {
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() !== '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('initialize', $event->getData());
        }
    }

    /**
     * startup
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function startup(Event $event)
    {
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('startup', $event->getData());
        }
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
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('beforeRender', $event->getData());
        }
    }

    /**
     * beforeRedirect
     *
     * @param Event $event
     * @return \Cake\Http\Response|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRedirect(Event $event)
    {
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return null;
            }
            /* @var Event $currentEvent */
            $currentEvent = $event->getSubject()->dispatchLayerEvent('beforeRedirect', $event->getData());
            if ($currentEvent) {
                $event->setData($currentEvent->getData());
                return $currentEvent->getResult();
            }
        }
        return null;
    }

    /**
     * shutdown
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function shutdown(Event $event)
    {
        if ($event->getSubject()->getName() != 'CakeError' && $event->getSubject()->getName() != '') {
            if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
                return;
            }
            $event->getSubject()->dispatchLayerEvent('shutdown', $event->getData());
        }
    }

}
