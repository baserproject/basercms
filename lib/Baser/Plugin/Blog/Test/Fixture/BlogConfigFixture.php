<?php

/**
 * BlogConfigFixture
 *
 */
App::uses('BlogConfig', 'Blog.Model');

class BlogConfigFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
		'model' => 'blog.BlogConfig',
		'records' => true,
		'connection' => 'plugin'
	);

}
