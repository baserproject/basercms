<?php
App::uses('BcEvent', 'Event');
class BcControllerEvent extends BcEvent {
	
	public $layer = 'Controller';
	
	public $passParams = array('\.beforeRedirect$');
	
}