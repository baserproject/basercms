<?php

App::uses('CakeErrorController', 'Controller');

class TestAppsErrorController extends CakeErrorController {

	public $helpers = [
		'Html',
		'Session',
		'Form',
		'Banana',
	];

}
