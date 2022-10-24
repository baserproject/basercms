<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BlogCategoriesFixture
 */
class BlogCategoriesFixture extends TestFixture
{

    public $import = ['table' => 'blog_categories'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'parent_id' => NULL,
            'lft' => 1,
            'rght' => 2,
            'owner_id' => NULL,
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ],
    ];

}
