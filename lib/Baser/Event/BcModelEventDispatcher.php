<?php
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
	 * @param CakeEvent $event
	 * @return array
	 */
	public function beforeFind(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return $event->data[0];
		}
		$currentEvent = $event->subject->dispatchEvent('beforeFind', $event->data);
		if ($currentEvent) {
			$event->data = $currentEvent->data;
			return true;
		}
		return $event->data[0];
	}

	/**
	 * afterFind
	 *
	 * @param type $event
	 * @return array
	 */
	public function afterFind(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return $event->data[0];
		}
		$currentEvent = $event->subject->dispatchEvent('afterFind', $event->data);
		if ($currentEvent) {
			$event->data = $currentEvent->data;
			return true;
		}
		return $event->data[0];
	}

	/**
	 * beforeValidate
	 *
	 * @param CakeEvent $event
	 * @return boolean
	 */
	public function beforeValidate(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return true;
		}
		$currentEvent = $event->subject->dispatchEvent('beforeValidate', $event->data);
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
	 * @param CakeEvent $event
	 * @return void
	 */
	public function afterValidate(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return;
		}
		$event->subject->dispatchEvent('afterValidate', $event->data);
	}

	/**
	 * beforeSave
	 *
	 * @param CakeEvent $event
	 * @return boolean
	 */
	public function beforeSave(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return true;
		}
		$currentEvent = $event->subject->dispatchEvent('beforeSave', $event->data);
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
	 * @param CakeEvent $event
	 * @return void
	 */
	public function afterSave(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return;
		}
		$event->subject->dispatchEvent('afterSave', $event->data);
	}

	/**
	 * beforeDelete
	 *
	 * @param CakeEvent $event
	 * @return boolean
	 */
	public function beforeDelete(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return true;
		}
		$currentEvent = $event->subject->dispatchEvent('beforeDelete', $event->data);
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
	 * @param CakeEvent $event
	 */
	public function afterDelete(CakeEvent $event)
	{
		if (!method_exists($event->subject(), 'dispatchEvent')) {
			return;
		}
		$event->subject->dispatchEvent('afterDelete', $event->data);
	}

}
