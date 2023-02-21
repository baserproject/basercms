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
     * Constructor
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
     */
    public function get(int $id, array $options = [])
    {
        $options = array_merge_recursive([
            'contain' => ['CustomFields']
        ], $options);
        return $this->CustomLinks->get($id, $options);
    }

    /**
     * 関連フィールドの一覧データを取得する
     *
     * @param int $tableId
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function getIndex(int $tableId, array $options = [])
    {
        $options = array_merge([
            'finder' => 'threaded',
            'status' => null,
            'for' => null
        ], $options);

        $conditions = ['CustomLinks.custom_table_id' => $tableId];
        if(!is_null($options['status']) && $options['status'] !== 'all') {
            $conditions['CustomLinks.status'] = $options['status'];
        }

        $findOptions = [];
        if(!is_null($options['for'])) {
            $findOptions['for'] = $options['for'];
        }

        return $this->CustomLinks->find($options['finder'], $findOptions)
            ->order('lft ASC')
            ->where($conditions)
            ->contain(['CustomFields']);
    }

    /**
     * 関連フィールドを編集する
     *
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     */
    public function update(EntityInterface $entity, array $postData)
    {
        $this->CustomLinks->getConnection()->begin();
        try {
            $oldName = $entity->name;
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

}
