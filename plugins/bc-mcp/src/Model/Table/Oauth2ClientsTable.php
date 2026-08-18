<?php
declare(strict_types=1);

namespace BcMcp\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Oauth2Clients Model
 *
 * @method \BcMcp\Model\Entity\Oauth2Client newEmptyEntity()
 * @method \BcMcp\Model\Entity\Oauth2Client newEntity(array $data, array $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client[] newEntities(array $data, array $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client get($primaryKey, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \BcMcp\Model\Entity\Oauth2Client[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class Oauth2ClientsTable extends Table
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

        $this->setTable('oauth2_clients');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // JSON文字列として保存し、取得時は配列として扱う
        // DBカラム型はtextだが、CakePHPの型マッピングでjsonを指定することで
        // 保存時に自動でエンコード、取得時に自動でデコードされる
        $this->getSchema()
            ->setColumnType('redirect_uris', 'json')
            ->setColumnType('grants', 'json')
            ->setColumnType('scopes', 'json');
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
            ->scalar('client_id')
            ->maxLength('client_id', 80)
            ->requirePresence('client_id', 'create')
            ->notEmptyString('client_id')
            ->add('client_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('client_secret')
            ->maxLength('client_secret', 80)
            ->allowEmptyString('client_secret');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        // JSONカラムは型を強制しない（スキーマのjson型マッピングで処理）
        $validator->allowEmptyString('redirect_uris');

        $validator->allowEmptyString('grants');

        $validator->allowEmptyString('scopes');

        $validator
            ->boolean('is_confidential')
            ->notEmptyString('is_confidential');

        $validator
            ->scalar('registration_access_token')
            ->maxLength('registration_access_token', 255)
            ->allowEmptyString('registration_access_token');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['client_id']), ['errorField' => 'client_id']);

        return $rules;
    }

    /**
     * Find client by client_id
     *
     * @param string $clientId
     * @return \BcMcp\Model\Entity\Oauth2Client|null
     */
    public function findByClientId(string $clientId): ?EntityInterface
    {
        return $this->find()
            ->where(['client_id' => $clientId])
            ->first();
    }

}
