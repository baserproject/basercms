<?php

/**
 * BlogCommentFixture
 *
 */
App::uses('BlogComment', 'Blog.Model');

class BlogCommentFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
		'model' => 'blog.BlogComment',
		'records' => true,
		'connection' => 'plugin'
	);

}
