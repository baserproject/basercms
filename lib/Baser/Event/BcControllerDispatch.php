<?php
class BcControllerDispatch extends Object implements CakeEventListener {
	
	public function implementedEvents() {
		return array(
			'Controller.initialize' => array('callable' => 'initialize'),
			'Controller.startup' => array('callable' => 'startup'),
			'Controller.beforeRender' => array('callable' => 'beforeRender'),
			'Controller.beforeRedirect' => array('callable' => 'beforeRedirect'),
			'Controller.shutdown' => array('callable' => 'shutdown'),
		);
	}
	function initialize($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.initialize', $event->data);
		}
	}
	function startup($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.startup', $event->data);
		}
	}
	function beforeRender($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.beforeRender', $event->data);
		}
	}
	function beforeRedirect($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.beforeRedirect', $event->data);
		}
	}
	function shutdown($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.shutdown', $event->data);
		}
	}

}