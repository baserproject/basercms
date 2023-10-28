<?php
declare(strict_types=1);

namespace SoftDelete\Test\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * PostsTags Model
 */
class PostsTagsTable extends Table
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
        $this->belongsTo('Tags');
        $this->belongsTo('Posts');
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
            ->integer('post_id')
            ->notEmptyString('post_id');
        $validator
            ->integer('tag_id')
            ->notEmptyString('tag_id');

        return $validator;
    }
}
