<?php
/**
 * FavoriteFixture
 *
 */
class FavoriteFixture extends CakeTestFixture {

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
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sort' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false),
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
			'user_id' => '1',
			'name' => '固定ページ管理',
			'url' => '/admin/pages/index',
			'sort' => '1',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '2',
			'user_id' => '1',
			'name' => '新着情報管理',
			'url' => '/admin/blog/blog_posts/index/1',
			'sort' => '2',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '3',
			'user_id' => '1',
			'name' => 'お問い合わせ管理',
			'url' => '/admin/mail/mail_fields/index/1',
			'sort' => '3',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '4',
			'user_id' => '1',
			'name' => '受信メール一覧',
			'url' => '/admin/mail/mail_messages/index/1',
			'sort' => '4',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '5',
			'user_id' => '1',
			'name' => 'コメント一覧',
			'url' => '/admin/blog/blog_comments/index/1',
			'sort' => '5',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
		array(
			'id' => '6',
			'user_id' => '1',
			'name' => 'クレジット',
			'url' => 'javascript:credit();',
			'sort' => '6',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		),
	);

}
