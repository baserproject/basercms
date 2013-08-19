<?php
class BcEvent extends Object implements CakeEventListener {

	public $events = array();
	
	public $layer = '';
	
    public function implementedEvents() {

		$events = array();
		if($this->events) {
			foreach($this->events as $registerEvent) {
				
				$eventName = $this->layer . '.' . $registerEvent;
				if(strpos($registerEvent, '.') !== false) {
					$aryRegisterEvent = explode('.', $registerEvent);
					$registerEvent = Inflector::variable(implode('_', $aryRegisterEvent));
				}
				$events[$eventName] = array('callable' => $registerEvent);
				
			}
		}
		
        return $events;
		
    }

}