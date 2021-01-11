<?php

/**
 * FavoriteFixture
 */
class FavoriteFixture extends BaserTestFixture
{

	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'Favorite';

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'user_id' => '1',
			'name' => '固定ページ管理',
			'url' => '/admin/pages/index',
			'sort' => '1',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '2',
			'user_id' => '1',
			'name' => '新着情報管理',
			'url' => '/admin/blog/blog_posts/index/1',
			'sort' => '2',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '3',
			'user_id' => '1',
			'name' => 'お問い合わせ管理',
			'url' => '/admin/mail/mail_fields/index/1',
			'sort' => '3',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '4',
			'user_id' => '1',
			'name' => '受信メール一覧',
			'url' => '/admin/mail/mail_messages/index/1',
			'sort' => '4',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '5',
			'user_id' => '1',
			'name' => 'コメント一覧',
			'url' => '/admin/blog/blog_comments/index/1',
			'sort' => '5',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
		[
			'id' => '6',
			'user_id' => '1',
			'name' => 'クレジット',
			'url' => 'javascript:credit();',
			'sort' => '6',
			'created' => '2015-01-27 12:57:59',
			'modified' => '2015-01-27 12:57:59'
		],
	];

}
