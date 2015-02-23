<?php
/**
 * MailContentFixture
 *
 */
class MailContentFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('connection' => 'baser');

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sender_1' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sender_2' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sender_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject_user' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject_admin' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'layout_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'form_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'mail_template' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'redirect_url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'auth_captcha' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'widget_area' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'ssl_on' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'name' => 'contact',
			'title' => 'お問い合わせ',
			'description' => '<p><span style="color:#C30">*</span> 印の項目は必須となりますので、必ず入力してください。</p>',
			'sender_1' => '',
			'sender_2' => '',
			'sender_name' => 'baserCMS inc. [デモ]　お問い合わせ',
			'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
			'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
			'layout_template' => 'default',
			'form_template' => 'default',
			'mail_template' => 'mail_default',
			'redirect_url' => 'http://basercms.net/',
			'status' => 1,
			'auth_captcha' => 1,
			'widget_area' => null,
			'ssl_on' => 0,
			'exclude_search' => 0,
			'publish_begin' => null,
			'publish_end' => null,
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		),
	);

}
