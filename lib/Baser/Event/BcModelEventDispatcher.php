<?php
class BcModelEventDispatcher extends Object implements CakeEventListener {
/**
 * implementedEvents
 * 
 * @return array
 */
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
	
/**
 * beforeFind
 * 
 * @param CakeEvent $event
 * @return array
 */
	public function beforeFind(CakeEvent $event) {
		$currentEvent = $event->subject->dispatchEvent('beforeFind', $event->data);
		if($currentEvent) {
			return $currentEvent->result === true ? $currentEvent->data[0] : $currentEvent->result;
		}
		return $event->data[0];
	}
	
/**
 * afterFind
 * 
 * @param type $event
 * @return array
 */
	public function afterFind(CakeEvent $event) {
		$currentEvent = $event->subject->dispatchEvent('afterFind', $event->data);
		if($currentEvent) {
			return $currentEvent->result;
		}
		return $event->data[0];
	}
	
/**
 * beforeValidate
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function beforeValidate(CakeEvent $event) {
		$currentEvent = $event->subject->dispatchEvent('beforeValidate', $event->data);
		if($currentEvent) {
			if ($currentEvent->isStopped()) {
				return false;
			}
		}
		return true;
	}
	
/**
 * afterValidate
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function afterValidate(CakeEvent $event) {
		$event->subject->dispatchEvent('afterValidate', $event->data);
	}
	
/**
 * beforeSave
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function beforeSave(CakeEvent $event) {
		$currentEvent = $event->subject->dispatchEvent('beforeSave', $event->data);
		if($currentEvent) {
			if (!$currentEvent->result) {
				return false;
			}
		}
		return true;
	}
	
/**
 * afterSave
 * 
 * @param CakeEvent $event
 * @return void
 */
	public function afterSave(CakeEvent $event) {
		$event->subject->dispatchEvent('afterSave', $event->data);
	}
	
/**
 * beforeDelete
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function beforeDelete(CakeEvent $event) {
		$currentEvent = $event->subject->dispatchEvent('beforeDelete', $event->data);
		if($currentEvent) {
			if ($event->isStopped()) {
				return false;
			}
		}
		return false;
	}
	
/**
 * afterDelete
 * 
 * @param CakeEvent $event
 */
	public function afterDelete(CakeEvent $event) {
		$event->subject->dispatchEvent('afterDelete', $event->data);
	}
	
}