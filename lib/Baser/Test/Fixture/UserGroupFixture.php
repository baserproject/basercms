<?php

/**
 * UserGroupFixture
 *
 */
class UserGroupFixture extends CakeTestFixture {

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
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50),
		'auth_prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20),
		'use_admin_globalmenu' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'default_favorites' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'modified' => array('type' => 'datetime', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
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
			'name' => 'admins',
			'title' => 'システム管理',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 1,
			'default_favorites' => 'a:6:{i:0;a:2:{s:4:"name";s:21:"固定ページ管理";s:3:"url";s:18:"/admin/pages/index";}i:1;a:2:{s:4:"name";s:18:"新着情報管理";s:3:"url";s:30:"/admin/blog/blog_posts/index/1";}i:2;a:2:{s:4:"name";s:24:"お問い合わせ管理";s:3:"url";s:31:"/admin/mail/mail_fields/index/1";}i:3;a:2:{s:4:"name";s:21:"受信メール一覧";s:3:"url";s:33:"/admin/mail/mail_messages/index/1";}i:4;a:2:{s:4:"name";s:18:"コメント一覧";s:3:"url";s:33:"/admin/blog/blog_comments/index/1";}i:5;a:2:{s:4:"name";s:15:"クレジット";s:3:"url";s:20:"javascript:credit();";}}',
			'modified' => null,
			'created' => '2012-11-04 02:46:27'
		),
		array(
			'id' => 2,
			'name' => 'operators',
			'title' => 'サイト運営',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 0,
			'default_favorites' => 'a:5:{i:0;a:2:{s:4:"name";s:21:"固定ページ管理";s:3:"url";s:18:"/admin/pages/index";}i:1;a:2:{s:4:"name";s:18:"新着情報管理";s:3:"url";s:30:"/admin/blog/blog_posts/index/1";}i:2;a:2:{s:4:"name";s:24:"お問い合わせ管理";s:3:"url";s:31:"/admin/mail/mail_fields/index/1";}i:3;a:2:{s:4:"name";s:21:"受信メール一覧";s:3:"url";s:33:"/admin/mail/mail_messages/index/1";}i:4;a:2:{s:4:"name";s:18:"コメント一覧";s:3:"url";s:33:"/admin/blog/blog_comments/index/1";}}',
			'modified' => null,
			'created' => '2012-11-04 02:46:27'
		),
		array(
			'id' => 3,
			'name' => 'admins_copy',
			'title' => 'システム管理_copy',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 1,
			'default_favorites' => 'a:6:{i:0;a:2:{s:4:"name";s:21:"固定ページ管理";s:3:"url";s:18:"/admin/pages/index";}i:1;a:2:{s:4:"name";s:18:"新着情報管理";s:3:"url";s:30:"/admin/blog/blog_posts/index/1";}i:2;a:2:{s:4:"name";s:24:"お問い合わせ管理";s:3:"url";s:31:"/admin/mail/mail_fields/index/1";}i:3;a:2:{s:4:"name";s:21:"受信メール一覧";s:3:"url";s:33:"/admin/mail/mail_messages/index/1";}i:4;a:2:{s:4:"name";s:18:"コメント一覧";s:3:"url";s:33:"/admin/blog/blog_comments/index/1";}i:5;a:2:{s:4:"name";s:15:"クレジット";s:3:"url";s:20:"javascript:credit();";}}',
			'modified' => '2012-11-04 03:53:47',
			'created' => '2012-11-04 03:53:47'
		),
		array(
			'id' => 4,
			'name' => 'user_group_test',
			'title' => 'ユーザグループテスト2',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 1,
			'default_favorites' => null,
			'modified' => '2012-11-04 06:02:23',
			'created' => '2012-11-04 06:01:38'
		),
		array(
			'id' => 5,
			'name' => 'user_group_test_copy',
			'title' => 'ユーザグループテスト2_copy',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 1,
			'default_favorites' => null,
			'modified' => '2012-11-04 06:02:30',
			'created' => '2012-11-04 06:02:30'
		),
	);

}
