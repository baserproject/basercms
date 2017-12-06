<?php

App::uses('CakeErrorController', 'Controller');

class TestConfigsController extends CakeErrorController {

	public $components = [
		'RequestHandler' => [
			'some' => 'config'
		]
	];

}
