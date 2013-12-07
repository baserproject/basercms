<?php

/**
 * MailContentFixture
 *
 */
App::uses('MailContent', 'Mail.Model');

class MailContentFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
		'model' => 'Mail.MailContent',
		'records' => true,
		'connection' => 'plugin'
	);

}
