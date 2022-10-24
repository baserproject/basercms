<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BlogContentFixture
 */
class BlogPostsBlogTagFixture extends TestFixture
{

    public $import = ['table' => 'blog_posts_blog_tags'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'blog_post_id' => '2',
            'blog_tag_id' => '1',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ],
    ];

}
