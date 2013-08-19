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
	public function beforeRenderFile(CakeEvent $event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.beforeRenderFile', $event->data);
		}
	}
	public function afterRenderFile($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.afterRenderFile', $event->data);
		}
	}
	public function beforeRender($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.beforeRender', $event->data);
		}
	}
	public function afterRender($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.afterRender', $event->data);
		}
	}
	public function beforeLayout($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.beforeLayout', $event->data);
		}
	}
	public function afterLayout($event) {
		if($event->subject->name != 'CakeError') {
			return $event->subject->dispatchEvent($event->subject->name . '.afterLayout', $event->data);
		}
	}
	
}