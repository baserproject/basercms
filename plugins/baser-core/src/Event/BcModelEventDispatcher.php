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

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcModelEventDispatcher
 *
 * モデルイベントディスパッチャ
 *
 * beforeFind 等の、CakePHPのモデル向け標準イベントについて、
 * モデルごとにイベントをディスパッチする。
 * bootstrap で、attach される。
 *
 * 《イベント名の命名規則》
 * Model.ModelName.eventName
 */
class BcModelEventDispatcher implements EventListenerInterface
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
            'Model.beforeFind' => 'beforeFind',
            'Model.afterFind' => 'afterFind',
            'Model.beforeMarshal' => 'beforeMarshal',
            'Model.afterMarshal' => 'afterMarshal',
            'Model.beforeSave' => 'beforeSave',
            'Model.afterSave' => 'afterSave',
            'Model.beforeDelete' => 'beforeDelete',
            'Model.afterDelete' => 'afterDelete'
        ];
    }

    /**
     * beforeFind
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFind(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            $event->setResult($event->getData(0));
            return;
        }
        $currentEvent = $event->getSubject()->dispatchLayerEvent('beforeFind', $event->getData());
        if ($currentEvent) {
            $event->setData($currentEvent->getData());
            $event->setResult(true);
            return;
        }
        $event->setResult($event->getData(0));
    }

    /**
     * afterFind
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterFind(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            $event->setResult($event->getData(0));
            return;
        }
        $currentEvent = $event->getSubject()->dispatchLayerEvent('afterFind', $event->getData());
        if ($currentEvent) {
            $event->setData($currentEvent->getData());
            $event->setResult(true);
            return;
        }
        $event->setResult($event->getData(0));
    }

    /**
     * beforeMarshal
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            $event->setResult(true);
            return;
        }
        $currentEvent = $event->getSubject()->dispatchLayerEvent('beforeMarshal', $event->getData());
        if ($currentEvent) {
            if ($currentEvent->isStopped()) {
                $event->setResult(false);
                return;
            }
        }
        $event->setResult(true);
    }

    /**
     * afterMarshal
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterMarshal(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            return;
        }
        $event->getSubject()->dispatchLayerEvent('afterMarshal', $event->getData());
    }

    /**
     * beforeSave
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            $event->setResult(true);
            return;
        }
        $currentEvent = $event->getSubject()->dispatchLayerEvent('beforeSave', $event->getData());
        if ($currentEvent) {
            if (!$currentEvent->getResult()) {
                $event->setResult(false);
                return;
            }
        }
        $event->setResult(true);
    }

    /**
     * afterSave
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterSave(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            return;
        }
        $event->getSubject()->dispatchLayerEvent('afterSave', $event->getData());
    }

    /**
     * beforeDelete
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeDelete(EventInterface $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            $event->setResult(true);
            return;
        }
        $currentEvent = $event->getSubject()->dispatchLayerEvent('beforeDelete', $event->getData());
        if ($currentEvent) {
            if ($event->isStopped()) {
                $event->setResult(false);
                return;
            }
        }
        $event->setResult(true);
    }

    /**
     * afterDelete
     *
     * @param Event $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterDelete(Event $event): void
    {
        if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
            return;
        }
        $event->getSubject()->dispatchLayerEvent('afterDelete', $event->getData());
    }

}
