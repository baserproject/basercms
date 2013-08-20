<?php
class BcEventDispatcher extends Object {
	
	public static function dispatch($layer, $name, $Class, $params = array(), $options = array()) {
		
		if($layer && !preg_match('/^' . $layer . './', $name)) {
			if($Class->plugin) {
				$name = $Class->plugin . '.' . $Class->name . '.' . $name;
			} else {
				$name = $Class->name . '.' . $name;
			}
			$name = $layer . '.' . $name;
		}
		
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