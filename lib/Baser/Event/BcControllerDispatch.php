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
		$event->subject->getEventManager()->dispatch(new CakeEvent('Controller.' . $event->subject->name . '.initialize', $this, $event->data));
	}
	function startup($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Controller.' . $event->subject->name . '.startup', $this, $event->data));
	}
	function beforeRender($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Controller.' . $event->subject->name . '.beforeRender', $this, $event->data));
	}
	function beforeRedirect($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Controller.' . $event->subject->name . '.beforeRedirect', $this, $event->data));
	}
	function shutdown($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Controller.' . $event->subject->name . '.shutdown', $this, $event->data));
	}

}