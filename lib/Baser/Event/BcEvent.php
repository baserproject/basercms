<?php
class BcEvent extends Object implements CakeEventListener {

	public $registerEvents = array();
	
	public $layer = '';
	
	public $passParams = array();
	
    public function implementedEvents() {

		$events = array();
		if($this->registerEvents) {
			foreach($this->registerEvents as $registerEvent) {
				
				$passParams = false;
				if($this->passParams && preg_match('/(' . implode('|', $this->passParams) . ')/', $registerEvent)) {
					$passParams = true;
				}
				$eventName = $this->layer . '.' . $registerEvent;
				if(strpos($registerEvent, '.') !== false) {
					$aryRegisterEvent = explode('.', $registerEvent);
					$registerEvent = Inflector::variable(implode('_', $aryRegisterEvent));
				}
				if($passParams) {
					$events[$eventName] = array('callable' => $registerEvent, 'passParams' => true);
				} else {
					$events[$eventName] = array('callable' => $registerEvent);
				}
				
			}
		}
		
        return $events;
		
    }

}