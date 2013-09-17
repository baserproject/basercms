<?php
/**
 * MessageFixture
 *
 */
App::uses('Message','Mail.Model');

class MessageFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
        'model' => 'Mail.Message',
        'records' => true,
        'connection' => 'plugin'
    );

}
