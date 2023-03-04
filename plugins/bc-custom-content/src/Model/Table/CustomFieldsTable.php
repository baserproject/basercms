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
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * CustomFieldsTable
 */
class CustomFieldsTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize
     *
     * @param array $config
     * @checked
     * @noTodo
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
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('user', 'BaserCore\Model\Validation\UserValidation');
        $validator
            ->scalar('name')
            ->notEmptyString('name', __d('baser_core', 'フィールド名を入力してください。'))
            ->maxLength('name', 255, __d('baser_core', 'フィールド名は255文字以内で入力してください。'))
            ->regex('name', '/^[a-z0-9_]+$/', __d('baser_core', 'フィールド名は半角英数字とアンダースコアのみで入力してください。'))
            ->add('name', [
                'validateUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるフィールド名です。')
                ]
            ]);
        $validator
            ->scalar('title')
            ->notEmptyString('title', __d('baser_core', '項目見出しを入力してください。'))
            ->maxLength('title', 255, __d('baser_core', '項目見出しは255文字以内で入力してください。'));
        $validator
            ->scalar('type')
            ->notEmptyString('type', __d('baser_core', 'タイプを入力してください。'));
        return $validator;
    }

    /**
     * Before Marshal
     *
     * @param EventInterface $event
     * @param ArrayObject $content
     * @param ArrayObject $options
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $content, ArrayObject $options)
    {
        // beforeMarshal のタイミングで変換しないと配列が null になってしまう
        if(!empty($content['meta'])) {
            $content['meta'] = json_encode($content['meta'], JSON_UNESCAPED_UNICODE);
        }
        if(!empty($content['validate'])) {
            $content['validate'] = json_encode($content['validate'], JSON_UNESCAPED_UNICODE);
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
     */
    public function findAll(Query $query, array $options = []): Query
    {
        return $query->formatResults(function (\Cake\Collection\CollectionInterface $results) {
            return $results->map(function ($row) {
                if(!$row) return $row;
                if($row->meta) $row->meta = json_decode($row->meta, true);
                if($row->validate) $row->validate = json_decode($row->validate, true);
                return $row;
            });
        });
    }

}
