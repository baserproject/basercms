<?php
declare(strict_types=1);

namespace SoftDelete\Test\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Posts Model
 */
class PostsTable extends Table
{
    use SoftDeleteTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->belongsTo('Users');
        $this->belongsToMany('Tags');
        $this->addBehavior('CounterCache', ['Users' => ['posts_count']]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        return $validator;
    }
}
