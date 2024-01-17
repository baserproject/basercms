<?php

namespace SoftDelete\Test\Fixture;

use Cake\ORM\Table;
use Cake\TestSuite\Fixture\TestFixture;

use SoftDelete\Model\Table\SoftDeleteTrait;

class PostsTable extends Table
{
    use SoftDeleteTrait;

    /**
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->belongsTo('Users');
        $this->belongsToMany('Tags');
        $this->addBehavior('CounterCache', ['Users' => ['posts_count']]);
    }
}


class PostsFixture extends TestFixture
{
    public $records = [
        [
            'id'          => 1,
            'user_id'     => 1,
            'deleted'     => null,
        ],
        [
            'id'          => 2,
            'user_id'     => 1,
            'deleted'     => null,
        ],
    ];
}
