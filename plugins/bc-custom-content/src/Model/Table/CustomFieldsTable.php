<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent\Model\Table;

use ArrayObject;
use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * CustomFieldsTable
 */
class CustomFieldsTable extends AppTable
{
    /**
     * Initialize
     *
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        // 関連フィールド削除時にエントリーのフィールドも削除する必要があるため setDependent は false とし、
        // サービスの delete メソッドで削除する
        $this->hasMany('CustomLinks')
            ->setClassName('BcCustomContent.CustomLinks')
            ->setForeignKey('custom_field_id')
            ->setDependent(false);
    }

    /**
     * 初期バリデーション設定
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('user', 'BaserCore\Model\Validation\UserValidation');
        $validator
            ->scalar('name')
            ->notEmptyString('name', __d('baser_core', 'フィールド名を入力してください。'))
            ->maxLength('name', 255, __d('baser_core', 'フィールド名は255文字以内で入力してください。'))
            ->regex('name', '/^[a-z0-9_]+$/', __d('baser_core', 'フィールド名は半角小文字英数字とアンダースコアのみで入力してください。'))
            ->add('name', [
                'validateUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるフィールド名です。')
                ]
            ])
            ->add('name', [
                'reserved' => [
                    'rule' => ['reserved'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'システム予約名称のため利用できません。')
            ]]);
        $validator
            ->scalar('title')
            ->notEmptyString('title', __d('baser_core', '項目見出しを入力してください。'))
            ->maxLength('title', 255, __d('baser_core', '項目見出しは255文字以内で入力してください。'));
        $validator
            ->scalar('type')
            ->notEmptyString('type', __d('baser_core', 'タイプを入力してください。'));

        $validator
            ->allowEmptyString('size')
            ->integer('size', __d('baser_core', '横幅サイズは整数を入力してください。'));

        $validator
            ->allowEmptyString('line')
            ->integer('line', __d('baser_core', '行数は整数を入力してください。'));

        $validator
            ->allowEmptyString('max_length')
            ->integer('max_length', __d('baser_core', '最大文字数は整数を入力してください。'));

        $validator
            ->add('source', [
                'checkSelectList' => [
                    'provider' => 'bc',
                    'rule' => ['checkSelectList'],
                    'message' => __d('baser_core', '選択リストに同じ項目を複数登録できません。')
                ]
            ]);
        $validator
            ->add('meta', [
                'checkAlphaNumericWithJson' => [
                    'rule' => ['checkWithJson', 'BcCustomContent.email_confirm', "/^[a-z0-9_]+$/"],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'Eメール比較先フィールド名は半角小文字英数字とアンダースコアのみで入力してください。')
                ],
            ])
            ->add('meta', [
                'checkMaxFileSizeWithJson' => [
                    'rule' => ['checkWithJson', 'BcCustomContent.max_file_size', "/^[0-9]+$/"],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'ファイルアップロードサイズ上限は整数値のみで入力してください。')
                ],
            ])
            ->add('meta', [
                'checkFileExtWithJson' => [
                    'rule' => ['checkWithJson', 'BcCustomContent.file_ext', "/^[a-z,]+$/"],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '拡張子を次の形式のようにカンマ（,）区切りで入力します。')
                ],
            ]);
        return $validator;
    }

    /**
     * Before Marshal
     *
     * @param EventInterface $event
     * @param ArrayObject $content
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $content, ArrayObject $options)
    {
        // beforeMarshal のタイミングで変換しないと配列が null になってしまう
        $this->encodeEntity($content);
    }

    /**
     * afterMarshal
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $data, ArrayObject $options)
    {
        $metaErrors = $entity->getError('meta');
        if (isset($metaErrors['checkAlphaNumericWithJson'])) {
            $entity->setError('meta.BcCustomContent.email_confirm', ['checkAlphaNumericWithJson' => $metaErrors['checkAlphaNumericWithJson']]);
        }
        if (isset($metaErrors['checkFileExtWithJson'])) {
            $entity->setError('meta.BcCustomContent.file_ext', ['checkFileExtWithJson' => $metaErrors['checkFileExtWithJson']]);
        }
        if (isset($metaErrors['checkMaxFileSizeWithJson'])) {
            $entity->setError('meta.BcCustomContent.max_file_size', ['checkMaxFileSizeWithJson' => $metaErrors['checkMaxFileSizeWithJson']]);
        }
    }

    /**
     * Find all
     *
     * JSON データをデコードする
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     */
    public function findAll(Query $query, array $options = []): Query
    {
        return $query->formatResults(function(\Cake\Collection\CollectionInterface $results) {
            return $results->map([$this, 'decodeEntity']);
        });
    }

    /**
     * エンティティをデコードする
     *
     * @param EntityInterface $entity
     * @return mixed
     * @unitTest
     */
    public function decodeEntity(EntityInterface|array|null $entity): EntityInterface|array|null
    {
        if (!$entity) return $entity;
        if (isset($entity->meta) && $entity->meta && is_string($entity->meta)) $entity->meta = json_decode($entity->meta, true);
        if (isset($entity->validate) && $entity->validate && is_string($entity->validate)) $entity->validate = json_decode($entity->validate, true);
        return $entity;
    }

    /**
     * エンティティをエンコードする
     *
     * @param ArrayObject $entity
     * @return ArrayObject
     * @checked
     * @noTodo
     * @unitTest
     */
    public function encodeEntity(ArrayObject $entity)
    {
        if (!empty($entity['meta'])) {
            $entity['meta'] = json_encode($entity['meta'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($entity['validate'])) {
            $entity['validate'] = json_encode($entity['validate'], JSON_UNESCAPED_UNICODE);
        }
        return $entity;
    }

}
