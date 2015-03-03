<?php
/**
 * BcControllerEventDispatcher
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * コントローラーイベントディスパッチャ
 *
 * beforeRender 等の、CakePHPのコントローラー向け標準イベントについて、
 * コントローラーごとにイベントをディスパッチする。
 * bootstrap で、attach される。
 * 
 * 《イベント名の命名規則》
 * Controller.ControllerName.eventName
 */
class BcControllerEventDispatcher extends Object implements CakeEventListener {

/**
 * implementedEvents
 * 
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Controller.initialize' => array('callable' => 'initialize'),
			'Controller.startup' => array('callable' => 'startup'),
			'Controller.beforeRender' => array('callable' => 'beforeRender'),
			'Controller.beforeRedirect' => array('callable' => 'beforeRedirect'),
			'Controller.shutdown' => array('callable' => 'shutdown'),
		);
	}

/**
 * initialize
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function initialize(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if(!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('initialize', $event->data);
		}
	}

/**
 * startup
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function startup(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if(!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('startup', $event->data);
		}
	}

/**
 * beforeRender
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function beforeRender(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if(!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('beforeRender', $event->data);
		}
	}

/**
 * beforeRedirect
 * 
 * @param CakeEvent $event
 * @return Responcse
 */
	public function beforeRedirect(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if(!method_exists($event->subject(), 'dispatchEvent')) {
				return null;
			}
			$currentEvent = $event->subject->dispatchEvent('beforeRedirect', $event->data);
			if ($currentEvent) {
				$event->data = $currentEvent->data;
				return $currentEvent->result;
			}
		}
		return null;
	}

/**
 * shutdown
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function shutdown(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			if(!method_exists($event->subject(), 'dispatchEvent')) {
				return;
			}
			$event->subject->dispatchEvent('shutdown', $event->data);
		}
	}

}
