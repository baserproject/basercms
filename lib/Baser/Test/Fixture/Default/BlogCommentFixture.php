<?php

/**
 * BlogCommentFixture
 */
class BlogCommentFixture extends BaserTestFixture
{

	/**
	 * Name of the object
	 *
	 * @var string
	 */
	public $name = 'BlogComment';

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => 1,
			'blog_content_id' => 1,
			'blog_post_id' => 1,
			'no' => 1,
			'status' => 1,
			'name' => 'baserCMS',
			'email' => '',
			'url' => 'https://basercms.net',
			'message' => 'ホームページの開設おめでとうございます。（ダミー）',
			'created' => '2015-08-10 18:57:47',
			'modified' => NULL,
		],
	];

}
