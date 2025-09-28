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

use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

/**
 * CustomContentsTable
 *
 * @property CustomTablesTable $CustomTables
 */
class CustomContentsTable extends AppTable
{

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
        $this->addBehavior('Timestamp');
        $this->belongsTo('CustomTables', ['className' => 'BcCustomContent.CustomTables'])
            ->setForeignKey('custom_table_id');
        if (Plugin::isLoaded('BcSearchIndex')) {
            $this->addBehavior('BcSearchIndex.BcSearchIndexManager');
        }
    }

    /**
     * デフォルトのバリデーションを設定する
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationWithTable(Validator $validator): Validator
    {
        $validator->setProvider('bc', 'BaserCore\Model\Validation\BcValidation');
        $validator->allowEmptyString('list_count')
            ->range('list_count', [0, 100], __d('baser_core', '一覧表示件数は100までの数値で入力してください。'))
            ->add('list_count', 'halfText', [
                'provider' => 'bc',
                'rule' => 'halfText',
                'message' => __d('baser_core', '一覧表示件数は半角で入力してください。')]);
        return $validator;
    }

    /**
     * beforeSave
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param \ArrayObject $options
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (!Plugin::isLoaded('BcSearchIndex')) {
            return true;
        }
        if (empty($entity->content) || !empty($entity->content->exclude_search)) {
            $this->setExcluded();
        }
        return true;
    }

    /**
     * 検索用データを生成する
     *
     * @param array $data
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createSearchIndex($entity)
    {
        if (!$entity || !isset($entity->content)) {
            return false;
        }
        return [
            'type' => __d('baser_core', 'カスタムコンテンツ'),
            'model_id' => $entity->id,
            'content_id' => $entity->content->id,
            'site_id' => $entity->content->site_id,
            'title' => $entity->content->title,
            'detail' => $entity->description,
            'url' => $entity->content->url,
            'status' => $entity->content->status,
            'publish_begin' => $entity->content->publish_begin,
            'publish_end' => $entity->content->publish_end
        ];
    }

    /**
     * 関連するエントリーの検索インデックスを作成する
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function createRelatedSearchIndexes(EntityInterface $entity)
    {
        if (!$entity || !isset($entity->custom_table_id)) {
            return;
        }
        $customEntriyTable = $this->CustomTables->getTableLocator()->get('BcCustomContent.CustomEntries');
        $customEntriyTable->setUp($entity->custom_table_id);

        $entries = $customEntriyTable->find()
            ->all();
        if (!$entries->count()) {
            return;
        }

        foreach ($entries as $entry) {
            // 入れ子になっているとエラーになるため、一旦配列に変換してから再度patchEntityする
            $entry = $entry->toArray();
            $entity = $customEntriyTable->patchEntity($customEntriyTable->newEmptyEntity(), $entry);
            $customEntriyTable->save($entity);
        }
    }
}
