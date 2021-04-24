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
	 * @param Event $event
	 * @return void
	 */
	public function beforeRenderFile(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('beforeRenderFile', $event->getData());
		}
	}

	/**
	 * afterRenderFile
	 *
	 * @param Event $event
	 * @return array
	 */
	public function afterRenderFile(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return $event->getData(1);
			}
			$currentEvent = $event->getSubject()->dispatchEvent('afterRenderFile', $event->getData(), ['modParams' => 1]);
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
	 */
	public function beforeRender(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('beforeRender', $event->data);
		}
	}

	/**
	 * afterRender
	 *
	 * @param Event $event
	 * @return void
	 */
	public function afterRender(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('afterRender', $event->getData());
		}
	}

	/**
	 * beforeLayout
	 *
	 * @param Event $event
	 * @return void
	 */
	public function beforeLayout(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('beforeLayout', $event->getData());
		}
	}

	/**
	 * afterLayout
	 *
	 * @param Event $event
	 * @return void
	 */
	public function afterLayout(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('afterLayout', $event->getData());
		}
	}

}
