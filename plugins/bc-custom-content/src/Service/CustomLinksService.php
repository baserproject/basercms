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

namespace BcCustomContent\Service;

use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Table\CustomLinksTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Hash;

/**
 * CustomLinksService
 *
 * @property CustomLinksTable $CustomLinks
 */
class CustomLinksService implements CustomLinksServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * CustomLinks Table
     * @var CustomLinksTable|Table
     */
    public CustomLinksTable|Table $CustomLinks;

    /**
     * Constructor
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->CustomLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomLinks');
    }

    /**
     * 関連フィールド単一データ取得
     *
     * @param int $id
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $options = [])
    {
        $options = array_merge([
            'status' => '',
            'contain' => [
                'CustomFields',
                'CustomTables' => ['CustomContents' => ['Contents']]
            ]
        ], $options);
        $conditions = [];
        if ($options['status'] === 'publish') {
            $conditions = $this->CustomLinks->CustomTables->CustomContents->Contents->getConditionAllowPublish();
            $conditions = array_merge($conditions, ['CustomLinks.status' => true]);
        }
        return $this->CustomLinks->get($id,
            contain: $options['contain'],
            conditions: $conditions
        );
    }

    /**
     * 関連フィールドの一覧データを取得する
     *
     * @param int $tableId
     * @param array $options
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(int $tableId, array $options = [])
    {
        $options = array_merge([
            'finder' => 'threaded',
            'status' => null,
            'for' => null,
            'contain' => [
                'CustomFields',
                'CustomTables' => [
                    'CustomContents' => ['Contents']
                ]
            ]
        ], $options);

        $findOptions = [];
        if (!is_null($options['for'])) {
            $findOptions['for'] = $options['for'];
        }

        $query = $this->CustomLinks->find($options['finder'], ...$findOptions)
            ->orderBy('CustomLinks.lft ASC');

        $conditions = ['CustomLinks.custom_table_id' => $tableId];

        if ($options['status'] === 'publish') {
            $options ['contain'] = ['CustomTables' => ['CustomContents' => ['Contents']]];
            $fields = $this->CustomLinks->getSchema()->columns();
            $query->select($fields);
            $conditions = array_merge(
                $conditions,
                ['CustomLinks.status' => true],
                $this->CustomLinks->CustomTables->CustomContents->Contents->getConditionAllowPublish()
            );
        }

        if (is_null($options['contain']))
            $options['contain'] = [];
        return $query->where($conditions)->contain($options['contain']);
    }

    /**
     * @param int $tableId
     * @return array
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(int $tableId): array
    {
        $conditions = ['CustomLinks.custom_table_id' => $tableId];
        return $this->CustomLinks
            ->find('list', keyField: 'id', valueField: 'title')
            ->where($conditions)->toArray();
    }

    /**
     * 関連フィールドを新規登録する
     *
     * @param array $postData
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $this->CustomLinks->getConnection()->begin();
        try {
            $entity = $this->CustomLinks->patchEntity($this->CustomLinks->newEmptyEntity(), $postData);
            $entity = $this->CustomLinks->saveOrFail($entity);
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            $customEntriesService->addField($entity->custom_table_id, $entity->name, $entity->type);
        } catch (\Throwable $e) {
            $this->CustomLinks->getConnection()->rollback();
            throw $e;
        }
        $this->CustomLinks->getConnection()->commit();
        return $entity;
    }

    /**
     * 関連フィールドを編集する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $entity, array $postData)
    {
        $this->CustomLinks->getConnection()->begin();
        try {
            $oldName = $entity->name;
            unset($postData['custom_field']);
            $entity = $this->CustomLinks->patchEntity($entity, $postData);
            $result = $this->CustomLinks->saveOrFail($entity);
            if($oldName !== $entity->name) {
                /** @var CustomEntriesService $customEntriesService */
                $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
                $customEntriesService->renameField($entity->custom_table_id, $oldName, $entity->name);
            }
        } catch (\Throwable $e) {
            $this->CustomLinks->getConnection()->rollback();
            throw $e;
        }
        $this->CustomLinks->getConnection()->commit();
        return $result;
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, $options = []): array
    {
        if ($field === 'parent_id') {
            return $this->getGroupList($options['tableId']);
        }
        return [];
    }

    /**
     * 関連フィールドのグループのリストを取得する
     *
     * @param int $tableId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getGroupList(int $tableId)
    {
        $query = $this->CustomLinks->find('list')
            ->contain(['CustomFields'])
            ->where([
                'CustomLinks.custom_table_id' => $tableId,
                'CustomFields.type' => 'group',
                'CustomLinks.status' => true
            ]);
        return $query->toArray();
    }

    /**
     * 関連フィールドを削除する
     *
     * エントリーテーブルのカラムも削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $this->CustomLinks->getConnection()->begin();
        try {
            $entity = $this->CustomLinks->get($id);
            $result = $this->CustomLinks->delete($entity);

            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            $customEntriesService->removeField($entity->custom_table_id, $entity->name);
        } catch (\Throwable $e) {
            $this->CustomLinks->getConnection()->rollback();
            throw $e;
        }
        $this->CustomLinks->getConnection()->commit();
        return $result;
    }

    /**
     * 削除されたフィールドの反映、並び順の更新を実行
     *
     * @param int $tableId
     * @param array $customLinks
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateFields(int $tableId, array $customLinks)
    {
        $this->deleteFields($tableId, $customLinks);
        $this->CustomLinks->updateSort($customLinks);
    }

    /**
     * 削除されたフィールドを反映する
     *
     * 存在対象のフィールド以外を削除する
     *
     * @param int $tableId
     * @param array $customLinks 存在対象のフィールド
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteFields(int $tableId, array $customLinks)
    {
        $entities = $this->CustomLinks->find()->where(['custom_table_id' => $tableId])->all();
        $existsIds = Hash::extract($customLinks, '{n}.id');
        foreach($entities as $entity) {
            if(!in_array($entity->id, $existsIds)) {
                $this->delete($entity->id);
            }
        }
    }

    /**
     * フィールド名をもとにカスタムリンクの単一データを取得する
     *
     * @param string $name
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findByName(string $name, array $queryParams = [])
    {
        $options = array_merge([
            'status' => null,
            'contain' => null
        ], $queryParams);
        $conditions = [
            'CustomLinks.name' => $name
        ];
        if(!is_null($options['status'])) $conditions = ['CustomLinks.status' => $options['status']];
        $query = $this->CustomLinks->find()->where($conditions);
        if(!is_null($options['contain'])) {
            $query->contain($options['contain']);
        }
        $entitiy = $query->first();
        if(!$entitiy) {
            return [];
        }
        return $query->first()->toArray();
    }

}
