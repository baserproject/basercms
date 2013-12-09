<?php

/**
 * BlogContentFixture
 *
 */
App::uses('BlogContent', 'Blog.Model');

class BlogContentFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
		'model' => 'blog.BlogContent',
		'records' => true,
		'connection' => 'plugin'
	);

}
