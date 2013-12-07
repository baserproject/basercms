<?php

/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 3.0.0
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
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
	function initialize(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('initialize', $event->data);
		}
	}

/**
 * startup
 * 
 * @param CakeEvent $event
 * @return void
 */
	function startup(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('startup', $event->data);
		}
	}

/**
 * beforeRender
 * 
 * @param CakeEvent $event
 * @return void
 */
	function beforeRender(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('beforeRender', $event->data);
		}
	}

/**
 * beforeRedirect
 * 
 * @param CakeEvent $event
 * @return Responcse
 */
	function beforeRedirect(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
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
	function shutdown(CakeEvent $event) {
		if ($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('shutdown', $event->data);
		}
	}

}
