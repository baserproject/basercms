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
	 */
	public function initialize(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('initialize', $event->getData());
		}
	}

	/**
	 * startup
	 *
	 * @param Event $event
	 * @return void
	 */
	public function startup(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('startup', $event->getData());
		}
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
			$event->getSubject()->dispatchEvent('beforeRender', $event->getData());
		}
	}

	/**
	 * beforeRedirect
	 *
	 * @param Event $event
	 * @return Responcse
	 */
	public function beforeRedirect(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return null;
			}
			$currentEvent = $event->getSubject()->dispatchEvent('beforeRedirect', $event->getData());
			if ($currentEvent) {
				$event->setData($currentEvent->getData());
				return $currentEvent->result;
			}
		}
		return null;
	}

	/**
	 * shutdown
	 *
	 * @param Event $event
	 * @return void
	 */
	public function shutdown(Event $event)
	{
		if ($event->getSubject()->name != 'CakeError' && $event->getSubject()->name != '') {
			if (!method_exists($event->getSubject(), 'dispatchEvent')) {
				return;
			}
			$event->getSubject()->dispatchEvent('shutdown', $event->getData());
		}
	}

}
