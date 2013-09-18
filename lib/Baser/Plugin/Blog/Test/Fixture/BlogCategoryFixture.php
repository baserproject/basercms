<?php
/**
 * BlogCategoryFixture
 *
 */
App::uses('BlogCategory','Blog.Model');
class BlogCategoryFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array(
        'model' => 'blog.BlogCategory',
        'records' => true,
        'connection' => 'plugin'
    );

}
