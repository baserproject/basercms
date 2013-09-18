<?php
/**
 * BlogTagFixture
 *
 */
App::uses('BlogTag','Blog.Model');
class BlogTagFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
    public $import = array(
        'model' => 'blog.BlogTag',
        'records' => true,
        'connection' => 'plugin'
    );
}
