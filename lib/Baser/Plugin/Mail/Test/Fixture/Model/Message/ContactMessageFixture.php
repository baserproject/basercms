<?php
/**
 * MessageFixture
 *
 */
class ContactMessageFixture extends BaserTestFixture {
/**
 * Name of the object
 *
 * @var string
 */
  public $name = 'ContactMessage';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'name_1' => array('type' => 'text'),
		'name_2' => array('type' => 'text'),
		'name_kana_1' => array('type' => 'text'),
		'name_kana_2' => array('type' => 'text'),
		'sex' => array('type' => 'text'),
		'email_1' => array('type' => 'text'),
		'email_2' => array('type' => 'text'),
		'tel_1' => array('type' => 'text'),
		'tel_2' => array('type' => 'text'),
		'tel_3' => array('type' => 'text'),
		'zip' => array('type' => 'text'),
		'address_1' => array('type' => 'text'),
		'address_2' => array('type' => 'text'),
		'address_3' => array('type' => 'text'),
		'category' => array('type' => 'text'),
		'message' => array('type' => 'text'),
		'root' => array('type' => 'text'),
		'root_etc' => array('type' => 'text'),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
	);

}
