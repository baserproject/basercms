<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Event;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

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
	 */
	public function implementedEvents(): array
	{
		return [
			'Model.beforeFind' => 'beforeFind',
			'Model.afterFind' => 'afterFind',
			'Model.beforeValidate' => 'beforeValidate',
			'Model.afterValidate' => 'afterValidate',
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
	 * @return array|true
	 */
	public function beforeFind(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return $event->getData(0);
		}
		$currentEvent = $event->getSubject()->dispatchLayerEvent('beforeFind', $event->getData());
		if ($currentEvent) {
			$event->setData($currentEvent->getData());
			return true;
		}
		return $event->getData(0);
	}

	/**
	 * afterFind
	 *
	 * @param Event $event
	 * @return array|true
	 */
	public function afterFind(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return $event->getData(0);
		}
		$currentEvent = $event->getSubject()->dispatchLayerEvent('afterFind', $event->getData());
		if ($currentEvent) {
			$event->setData($currentEvent->getData());
			return true;
		}
		return $event->getData(0);
	}

	/**
	 * beforeValidate
	 *
	 * @param Event $event
	 * @return boolean
	 */
	public function beforeValidate(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return true;
		}
		$currentEvent = $event->getSubject()->dispatchLayerEvent('beforeValidate', $event->getData());
		if ($currentEvent) {
			if ($currentEvent->isStopped()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * afterValidate
	 *
	 * @param Event $event
	 * @return void
	 */
	public function afterValidate(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return;
		}
		$event->getSubject()->dispatchLayerEvent('afterValidate', $event->getData());
	}

	/**
	 * beforeSave
	 *
	 * @param Event $event
	 * @return boolean
	 */
	public function beforeSave(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return true;
		}
		$currentEvent = $event->getSubject()->dispatchLayerEvent('beforeSave', $event->getData());
		if ($currentEvent) {
			if (!$currentEvent->result) {
				return false;
			}
		}
		return true;
	}

	/**
	 * afterSave
	 *
	 * @param Event $event
	 * @return void
	 */
	public function afterSave(Event $event)
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
	 * @return boolean
	 */
	public function beforeDelete(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return true;
		}
		$currentEvent = $event->getSubject()->dispatchLayerEvent('beforeDelete', $event->getData());
		if ($currentEvent) {
			if ($event->isStopped()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * afterDelete
	 *
	 * @param Event $event
	 */
	public function afterDelete(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchLayerEvent')) {
			return;
		}
		$event->getSubject()->dispatchLayerEvent('afterDelete', $event->getData());
	}

}
