<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Event
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

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
class BcModelEventDispatcher extends CakeObject implements CakeEventListener
{

	/**
	 * implementedEvents
	 *
	 * @return array
	 */
	public function implementedEvents()
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
	 * @return array
	 */
	public function beforeFind(Event $event)
	{
		if (!method_exists($event->getSubject()(), 'dispatchEvent')) {
			return $event->getData(0);
		}
		$currentEvent = $event->getSubject()->dispatchEvent('beforeFind', $event->data);
		if ($currentEvent) {
			$event->setData($currentEvent->getData());
			return true;
		}
		return $event->getData(0);
	}

	/**
	 * afterFind
	 *
	 * @param type $event
	 * @return array
	 */
	public function afterFind(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return $event->getData(0);
		}
		$currentEvent = $event->getSubject()->dispatchEvent('afterFind', $event->getData());
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
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return true;
		}
		$currentEvent = $event->getSubject()->dispatchEvent('beforeValidate', $event->getData());
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
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return;
		}
		$event->getSubject()->dispatchEvent('afterValidate', $event->getData());
	}

	/**
	 * beforeSave
	 *
	 * @param Event $event
	 * @return boolean
	 */
	public function beforeSave(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return true;
		}
		$currentEvent = $event->getSubject()->dispatchEvent('beforeSave', $event->getData());
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
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return;
		}
		$event->getSubject()->dispatchEvent('afterSave', $event->getData());
	}

	/**
	 * beforeDelete
	 *
	 * @param Event $event
	 * @return boolean
	 */
	public function beforeDelete(Event $event)
	{
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return true;
		}
		$currentEvent = $event->getSubject()->dispatchEvent('beforeDelete', $event->getData());
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
		if (!method_exists($event->getSubject(), 'dispatchEvent')) {
			return;
		}
		$event->getSubject()->dispatchEvent('afterDelete', $event->getData());
	}

}
