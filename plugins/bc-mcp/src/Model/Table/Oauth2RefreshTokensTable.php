<?php
declare(strict_types=1);

namespace BcMcp\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Oauth2RefreshTokens Table
 */
class Oauth2RefreshTokensTable extends Table
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

        $this->setTable('oauth2_refresh_tokens');
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
            ->scalar('access_token_id')
            ->maxLength('access_token_id', 100)
            ->requirePresence('access_token_id', 'create')
            ->notEmptyString('access_token_id');

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
     * 期限切れのリフレッシュトークンをクリーンアップ
     *
     * @return int 削除された件数
     */
    public function cleanExpiredTokens(): int
    {
        return $this->deleteAll(['expires_at <' => new \DateTime()]);
    }

}
