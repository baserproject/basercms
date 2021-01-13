<?php

/**
 * PermissionFixture
 */
class PermissionFixture extends BaserTestFixture
{

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'no' => '1',
			'sort' => '1',
			'name' => 'システム管理',
			'user_group_id' => '2',
			'url' => '/admin/*',
			'auth' => 0,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '2',
			'no' => '2',
			'sort' => '2',
			'name' => 'よく使う項目',
			'user_group_id' => '2',
			'url' => '/admin/favorites/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '3',
			'no' => '3',
			'sort' => '3',
			'name' => 'ページ管理',
			'user_group_id' => '2',
			'url' => '/admin/pages/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '4',
			'no' => '4',
			'sort' => '4',
			'name' => 'ページテンプレート読込・書出',
			'user_group_id' => '2',
			'url' => '/admin/pages/*_page_files',
			'auth' => 0,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '5',
			'no' => '5',
			'sort' => '5',
			'name' => 'ページカテゴリ管理',
			'user_group_id' => '2',
			'url' => '/admin/page_categories/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '6',
			'no' => '6',
			'sort' => '6',
			'name' => '新着情報基本設定',
			'user_group_id' => '2',
			'url' => '/admin/blog/blog_contents/edit/1',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '7',
			'no' => '7',
			'sort' => '7',
			'name' => '新着情報記事管理',
			'user_group_id' => '2',
			'url' => '/admin/blog/blog_posts/*/1/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '8',
			'no' => '8',
			'sort' => '8',
			'name' => '新着情報記事プレビュー',
			'user_group_id' => '2',
			'url' => '/admin/blog/blog/preview/1/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '9',
			'no' => '9',
			'sort' => '9',
			'name' => '新着情報カテゴリ管理',
			'user_group_id' => '2',
			'url' => '/admin/blog/blog_categories/*/1/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '10',
			'no' => '10',
			'sort' => '10',
			'name' => '新着情報コメント一覧',
			'user_group_id' => '2',
			'url' => '/admin/blog/blog_comments/*/1/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '11',
			'no' => '11',
			'sort' => '11',
			'name' => 'ブログタグ管理',
			'user_group_id' => '2',
			'url' => '/admin/blog/blog_tags/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '12',
			'no' => '12',
			'sort' => '12',
			'name' => 'お問い合わせ基本設定',
			'user_group_id' => '2',
			'url' => '/admin/mail/mail_contents/edit/1',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '13',
			'no' => '13',
			'sort' => '13',
			'name' => 'お問い合わせ管理',
			'user_group_id' => '2',
			'url' => '/admin/mail/mail_fields/*/1/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '14',
			'no' => '14',
			'sort' => '14',
			'name' => 'お問い合わせ受信メール一覧',
			'user_group_id' => '2',
			'url' => '/admin/mail/mail_messages/*/1/*',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
		[
			'id' => '15',
			'no' => '15',
			'sort' => '15',
			'name' => 'エディタテンプレート呼出',
			'user_group_id' => '2',
			'url' => '/admin/editor_templates/js',
			'auth' => 1,
			'status' => 1,
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		],
	];

}
