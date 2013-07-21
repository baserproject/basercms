<?php
/**
 * MailFieldFixture
 *
 */
App::uses('MailField','Mail.Model');

class MailFieldFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
        'model' => 'Mail.MailField',
        'records' => true,
        'connection' => 'plugin'
    );

}
