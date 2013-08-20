<?php
class BcControllerEventDispatcher extends Object implements CakeEventListener {
	
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
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent($event->subject->name . '.initialize', $event->data);
		}
	}
/**
 * startup
 * 
 * @param CakeEvent $event
 * @return void
 */
	function startup(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent($event->subject->name . '.startup', $event->data);
		}
	}
/**
 * beforeRender
 * 
 * @param CakeEvent $event
 * @return void
 */
	function beforeRender(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent($event->subject->name . '.beforeRender', $event->data);
		}
	}
/**
 * beforeRedirect
 * 
 * @param CakeEvent $event
 * @return Responcse
 */
	function beforeRedirect(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$currentEvent = $event->subject->dispatchEvent($event->subject->name . '.beforeRedirect', $event->data);
			if($currentEvent) {
				return $currentEvent->result;
			}
		}
		return $event->data;
	}
/**
 * shutdown
 * 
 * @param CakeEvent $event
 * @return void
 */
	function shutdown(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			$event->subject->dispatchEvent($event->subject->name . '.shutdown', $event->data);
		}
	}

}