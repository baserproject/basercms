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
 * ビューイベントディスパッチャ
 *
 * beforeRender 等の、CakePHPのビュー向け標準イベントについて、
 * コントローラーごとにイベントをディスパッチする。
 * bootstrap で、attach される。
 * 
 * 《イベント名の命名規則》
 * View.ControllerName.eventName
 */
class BcViewEventDispatcher extends Object implements CakeEventListener {
	
	public function implementedEvents() {
		return array(
			'View.beforeRenderFile' => 'beforeRenderFile',
			'View.afterRenderFile' => 'afterRenderFile',
			'View.beforeRender' => 'beforeRender',
			'View.afterRender' => 'afterRender',
			'View.beforeLayout' => 'beforeLayout',
			'View.afterLayout' => 'afterLayout'
		);
	}
/**
 * beforeRenderFile
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function beforeRenderFile(CakeEvent $event) {
		if($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('beforeRenderFile', $event->data);
		}
	}
/**
 * afterRenderFile
 * 
 * @param CakeEvent $event
 * @return array
 */
	public function afterRenderFile(CakeEvent $event) {
		if($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$currentEvent = $event->subject->dispatchEvent('afterRenderFile', $event->data, array('modParams' => 1));
			if($currentEvent) {
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
	public function beforeRender(CakeEvent $event) {
		if($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('beforeRender', $event->data);
		}
	}
/**
 * afterRender
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function afterRender(CakeEvent $event) {
		if($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('afterRender', $event->data);
		}
	}
/**
 * beforeLayout
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function beforeLayout(CakeEvent $event) {
		if($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('beforeLayout', $event->data);
		}
	}
/**
 * afterLayout
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function afterLayout(CakeEvent $event) {
		if($event->subject->name != 'CakeError' && $event->subject->name != '') {
			$event->subject->dispatchEvent('afterLayout', $event->data);
		}
	}
	
}