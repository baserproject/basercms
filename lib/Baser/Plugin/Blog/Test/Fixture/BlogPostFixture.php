<?php
/**
 * BlogPostFixture
 *
 */
App::uses('BlogPost','Blog.Model');
class BlogPostFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
    public $import = array(
        'model' => 'blog.BlogPost',
        'records' => true,
        'connection' => 'plugin'
    );
}
