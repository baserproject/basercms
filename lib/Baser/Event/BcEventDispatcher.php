<?php
class BcEventDispatcher extends Object {
	
	public static function dispatch($Class, $name, $params = array(), $options = array()) {
		
		$options = array_merge(array('modParams' => 0), $options);
		extract($options);
		$EventManager = $Class->getEventManager();
		if(!$EventManager->listeners($name) && !CakeEventManager::instance()->listeners($name)) {
			return false;
		}
		
		$event = new CakeEvent($name, $Class, $params);
		$event->modParams = $modParams;
		$EventManager->dispatch($event);
		
		return $event;
		
	}
	
}