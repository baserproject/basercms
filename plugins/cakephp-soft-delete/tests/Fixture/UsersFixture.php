<?php

namespace SoftDelete\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\ORM\Table;

use SoftDelete\Model\Table\SoftDeleteTrait;

class UsersTable extends Table
{
    use SoftDeleteTrait;

    /**
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->hasMany('Posts', [
            'dependent'        => true,
            'cascadeCallbacks' => true,
        ]);
    }
}

class UsersFixture extends TestFixture
{
    public $records = [
        [
            'id'          => 1,
            'deleted'     => null,
            'posts_count' => 2
        ],
        [
            'id'          => 2,
            'deleted'     => null,
            'posts_count' => 0
        ],
    ];
}
