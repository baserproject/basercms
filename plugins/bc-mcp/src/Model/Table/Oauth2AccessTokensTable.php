<?php
declare(strict_types=1);

namespace BcMcp\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Oauth2AccessTokens Table
 */
class Oauth2AccessTokensTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('oauth2_access_tokens');
        $this->setDisplayField('token_id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('token_id')
            ->maxLength('token_id', 100)
            ->requirePresence('token_id', 'create')
            ->notEmptyString('token_id')
            ->add('token_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('client_id')
            ->maxLength('client_id', 100)
            ->requirePresence('client_id', 'create')
            ->notEmptyString('client_id');

        $validator
            ->scalar('user_id')
            ->maxLength('user_id', 100)
            ->allowEmptyString('user_id');

        $validator
            ->scalar('scopes')
            ->maxLength('scopes', 500)
            ->allowEmptyString('scopes');

        $validator
            ->boolean('revoked')
            ->notEmptyString('revoked');

        $validator
            ->dateTime('expires_at')
            ->requirePresence('expires_at', 'create')
            ->notEmptyDateTime('expires_at');

        return $validator;
    }

    /**
     * 期限切れのアクセストークンをクリーンアップ
     *
     * @return int 削除された件数
     */
    public function cleanExpiredTokens(): int
    {
        return $this->deleteAll(['expires_at <' => new \DateTime()]);
    }

}
