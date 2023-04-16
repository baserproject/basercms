<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BlogTagsFixture
 */
class BlogTagsFixture extends TestFixture
{

    public $import = ['table' => 'blog_tags'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'name' => '新製品',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ],
    ];

}
