<?php

namespace SoftDelete\Test\Fixture;

use Cake\ORM\Table;
use Cake\TestSuite\Fixture\TestFixture;

use SoftDelete\Model\Table\SoftDeleteTrait;

class PostsTagsTable extends Table
{
    use SoftDeleteTrait;

    /**
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->belongsTo('Tags');
        $this->belongsTo('Posts');
    }
}


class PostsTagsFixture extends TestFixture
{
    public $records = [
        [
            'id' => 1,
            'post_id' => 1,
            'tag_id' => 1,
            'deleted' => null,
        ],
        [
            'id' => 2,
            'post_id' => 1,
            'tag_id' => 2,
            'deleted' => '2015-05-18 15:04:00',
        ],
    ];
}
