<?php
class BcViewDispatch extends Object implements CakeEventListener {
	
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
	public function beforeRenderFile($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('View.' . $event->subject->name . '.beforeRenderFile', $this, $event->data));
	}
	public function afterRenderFile($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('View.' . $event->subject->name . '.afterRenderFile', $this, $event->data));
	}
	public function beforeRender($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('View.' . $event->subject->name . '.beforeRender', $this, $event->data));
	}
	public function afterRender($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('View.' . $event->subject->name . '.afterRender', $this, $event->data));
	}
	public function beforeLayout($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('View.' . $event->subject->name . '.beforeLayout', $this, $event->data));
	}
	public function afterLayout($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('View.' . $event->subject->name . '.afterLayout', $this, $event->data));
	}
	
}