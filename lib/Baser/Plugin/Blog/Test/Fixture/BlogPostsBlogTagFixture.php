<?php
/**
 * BlogPostsBlogTagFixture
 *
 */
App::uses('BlogPostsBlogTag','Blog.Model');
class BlogPostsBlogTagFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
    public $import = array(
        'model' => 'blog.BlogPostsBlogTag',
        'records' => true,
        'connection' => 'plugin'
    );
}
