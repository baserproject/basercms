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
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false),
		'name' => array('type' => 'string', 'null' => false),
		'url' => array('type' => 'string', 'null' => false),
		'sort' => array('type' => 'integer', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'name' => '固定ページ管理',
			'url' => '/admin/pages/index',
			'sort' => 1,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 02:47:11'
		),
		array(
			'id' => 2,
			'user_id' => 1,
			'name' => '新着情報管理',
			'url' => '/admin/blog/blog_posts/index/1',
			'sort' => 2,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 02:47:11'
		),
		array(
			'id' => 3,
			'user_id' => 1,
			'name' => 'お問い合わせ管理',
			'url' => '/admin/mail/mail_fields/index/1',
			'sort' => 3,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 02:47:11'
		),
		array(
			'id' => 4,
			'user_id' => 1,
			'name' => '受信メール一覧',
			'url' => '/admin/mail/mail_messages/index/1',
			'sort' => 4,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 02:47:11'
		),
		array(
			'id' => 5,
			'user_id' => 1,
			'name' => 'コメント一覧',
			'url' => '/admin/blog/blog_comments/index/1',
			'sort' => 5,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 02:47:11'
		),
		array(
			'id' => 6,
			'user_id' => 1,
			'name' => 'クレジット',
			'url' => 'javascript:credit();',
			'sort' => 6,
			'created' => '2012-11-04 02:47:11',
			'modified' => '2012-11-04 02:47:11'
		),
		array(
			'id' => 7,
			'user_id' => 2,
			'name' => '固定ページ管理',
			'url' => '/admin/pages/index',
			'sort' => 7,
			'created' => '2012-11-04 05:09:02',
			'modified' => '2012-11-04 05:09:02'
		),
		array(
			'id' => 8,
			'user_id' => 2,
			'name' => '新着情報管理',
			'url' => '/admin/blog/blog_posts/index/1',
			'sort' => 8,
			'created' => '2012-11-04 05:09:02',
			'modified' => '2012-11-04 05:09:02'
		),
		array(
			'id' => 9,
			'user_id' => 2,
			'name' => 'お問い合わせ管理',
			'url' => '/admin/mail/mail_fields/index/1',
			'sort' => 9,
			'created' => '2012-11-04 05:09:02',
			'modified' => '2012-11-04 05:09:02'
		),
		array(
			'id' => 10,
			'user_id' => 2,
			'name' => '受信メール一覧',
			'url' => '/admin/mail/mail_messages/index/1',
			'sort' => 10,
			'created' => '2012-11-04 05:09:02',
			'modified' => '2012-11-04 05:09:02'
		),
	);

}
