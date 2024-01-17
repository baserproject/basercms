<?php
declare(strict_types=1);

namespace SoftDelete\Test\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use SoftDelete\Model\Table\SoftDeleteTrait;

/**
 * Tags Model
 */
class TagsTable extends Table
{
    use SoftDeleteTrait;

    /**
     * Soft deletion field
     */
    public $softDeleteField = 'deleted_date';

    /**
     * Initialize method
     *
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
            ->requirePresence('name')
            ->allowEmptyString('name');

        return $validator;
    }
}
