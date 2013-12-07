<?php

/**
 * MailConfigFixture
 *
 */
App::uses('MailConfig', 'Mail.Model');

class MailConfigFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
		'model' => 'Mail.MailConfig',
		'records' => true,
		'connection' => 'plugin'
	);

}
