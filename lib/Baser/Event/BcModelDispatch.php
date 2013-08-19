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
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.beforeFind', $event->data);
	}
	public function afterFind($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.afterFind', $event->data);
	}
	public function beforeValidate($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.beforeValidate', $event->data);
	}
	public function afterValidate($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.afterValidate', $event->data);
	}
	public function beforeSave($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.beforeSave', $event->data);
	}
	public function afterSave($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.afterSave', $event->data);
	}
	public function beforeDelete($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.beforeDelete', $event->data);
	}
	public function afterDelete($event) {
		return true;
		return $event->subject->dispatchEvent($event->subject->name . '.afterDelete', $event->data);
	}
	
}