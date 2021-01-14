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
class BcViewEventDispatcher extends CakeObject implements CakeEventListener
{

	public function implementedEvents()
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
	 * @param CakeEvent $event
	 * @return void
	 */
	public function beforeRenderFile(CakeEvent $event)
	{
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if (!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('beforeRenderFile', $event->data);
		}
	}

	/**
	 * afterRenderFile
	 *
	 * @param CakeEvent $event
	 * @return array
	 */
	public function afterRenderFile(CakeEvent $event)
	{
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if (!method_exists($event->subject(), 'dispatchEvent')) {
				return $event->data[1];
			}
			$currentEvent = $event->subject->dispatchEvent('afterRenderFile', $event->data, ['modParams' => 1]);
			if ($currentEvent) {
				return $currentEvent->data[1];
			}
		}
		return $event->data[1];
	}

	/**
	 * beforeRender
	 *
	 * @param CakeEvent $event
	 * @return void
	 */
	public function beforeRender(CakeEvent $event)
	{
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if (!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('beforeRender', $event->data);
		}
	}

	/**
	 * afterRender
	 *
	 * @param CakeEvent $event
	 * @return void
	 */
	public function afterRender(CakeEvent $event)
	{
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if (!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('afterRender', $event->data);
		}
	}

	/**
	 * beforeLayout
	 *
	 * @param CakeEvent $event
	 * @return void
	 */
	public function beforeLayout(CakeEvent $event)
	{
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if (!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('beforeLayout', $event->data);
		}
	}

	/**
	 * afterLayout
	 *
	 * @param CakeEvent $event
	 * @return void
	 */
	public function afterLayout(CakeEvent $event)
	{
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if (!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('afterLayout', $event->data);
		}
	}

}
