<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BlogCommentsFixture
 */
class BlogCommentsFixture extends TestFixture
{

    public $import = ['table' => 'blog_comments'];

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
