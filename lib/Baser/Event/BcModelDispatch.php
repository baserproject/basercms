<?php
class BcModelDispatch extends Object implements CakeEventListener {
	
	public function implementedEvents() {
		return array(
			'Model.beforeFind' => 'beforeFind',
			'Model.afterFind' => 'afterFind',
			'Model.beforeValidate' => 'beforeValidate',
			'Model.afterValidate' => 'afterValidate',
			'Model.beforeSave' => 'beforeSave',
			'Model.afterSave' => 'afterSave',
			'Model.beforeDelete' => 'beforeDelete',
			'Model.afterDelete' => 'afterDelete'
		);
	}
	public function beforeFind($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.beforeFind', $this, $event->data));
	}
	public function afterFind($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.afterFind', $this, $event->data));
	}
	public function beforeValidate($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.beforeValidate', $this, $event->data));
	}
	public function afterValidate($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.afterValidate', $this, $event->data));
	}
	public function beforeSave($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.beforeSave', $this, $event->data));
	}
	public function afterSave($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.afterSave', $this, $event->data));
	}
	public function beforeDelete($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.beforeDelete', $this, $event->data));
	}
	public function afterDelete($event) {
		$event->subject->getEventManager()->dispatch(new CakeEvent('Model.' . $event->subject->name . '.afterDelete', $this, $event->data));
	}
	
}