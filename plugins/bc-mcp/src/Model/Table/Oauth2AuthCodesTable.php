<?php
declare(strict_types=1);

namespace BcMcp\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Oauth2AuthCodes Table
 */
class Oauth2AuthCodesTable extends Table
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

        $this->setTable('oauth2_auth_codes');
        $this->setDisplayField('code');
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
            ->scalar('code')
            ->maxLength('code', 100)
            ->requirePresence('code', 'create')
            ->notEmptyString('code')
            ->add('code', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('user_id')
            ->maxLength('user_id', 100)
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->scalar('client_id')
            ->maxLength('client_id', 80)
            ->requirePresence('client_id', 'create')
            ->notEmptyString('client_id');

        $validator
            ->scalar('redirect_uri')
            ->requirePresence('redirect_uri', 'create')
            ->notEmptyString('redirect_uri');

        $validator
            ->scalar('scopes')
            ->allowEmptyString('scopes');

        $validator
            ->boolean('revoked')
            ->notEmptyString('revoked');

        $validator
            ->dateTime('expires_at')
            ->requirePresence('expires_at', 'create')
            ->notEmptyDateTime('expires_at');

        $validator
            ->scalar('code_challenge')
            ->maxLength('code_challenge', 255)
            ->allowEmptyString('code_challenge');

        $validator
            ->scalar('code_challenge_method')
            ->maxLength('code_challenge_method', 255)
            ->allowEmptyString('code_challenge_method');

        return $validator;
    }

    /**
     * 期限切れの認可コードをクリーンアップ
     *
     * @return int 削除された件数
     */
    public function cleanExpiredCodes(): int
    {
        return $this->deleteAll(['expires_at <' => new \DateTime()]);
    }

}
