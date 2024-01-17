<?php

namespace SoftDelete\Test\Fixture;

use Cake\ORM\Table;
use Cake\TestSuite\Fixture\TestFixture;

use SoftDelete\Model\Table\SoftDeleteTrait;

class TagsTable extends Table
{
    use SoftDeleteTrait;

    protected $softDeleteField = 'deleted_date';

    /**
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->belongsToMany('Posts', [
            'through' => 'PostsTags',
            'joinTable' => 'posts_tags',
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'post_id'
        ]);
        $this->hasMany('PostsTags');
    }
}


class TagsFixture extends TestFixture
{
    public $records = [
        [
            'id' => 1,
            'name' => 'Cat',
            'deleted_date' => null,
        ],
        [
            'id' => 2,
            'name' => 'Dog',
            'deleted_date' => null,
        ],
        [
            'id' => 3,
            'name' => 'Fish',
            'deleted_date' => '2015-04-15 09:46:00',
        ]
    ];
}
