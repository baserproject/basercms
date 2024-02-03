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

use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Entity\CustomTable;
use BcCustomContent\Model\Table\CustomTablesTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomTablesService
 *
 * @property CustomTablesTable $CustomTables
 */
class CustomTablesService implements CustomTablesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Constructor
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->CustomTables = TableRegistry::getTableLocator()->get('BcCustomContent.CustomTables');
    }

    /**
     * カスタムテーブルの初期値となるエンティティを取得する
     *
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew()
    {
        return $this->CustomTables->newEntity([
            'type' => 1,
            'display_field' => 'title'
        ]);
    }

    /**
     * カスタムテーブルの単一データを取得する
     *
     * @param int $id
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, $options = [])
    {
        return $this->CustomTables->get($id, $options);
    }

    /**
     * カスタムコンテンツを持っているかどうか判定
     *
     * @param int $tableId
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hasCustomContent(int $tableId)
    {
        return (bool) $this->CustomTables->CustomContents->find()
            ->where(['CustomContents.custom_table_id' => $tableId])
            ->contain(['Contents'])
            ->count();
    }

    /**
     * カスタムテーブルについてカスタムコンテンツと関連フィールドを一緒に取得する
     *
     * @param int $tableId
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getWithContentAndLinks(int $tableId)
    {
        return $this->get($tableId, [
            'contain' => [
                'CustomLinks' => [
                    'conditions' => ['CustomLinks.status' => true],
                    'CustomFields'
                ],
                'CustomContents' => [
                    'Contents' => ['Sites']
                ]
        ]]);
    }

    /**
     * カスタムテーブルについて関連フィールドを一緒に取得する
     *
     * @param int $tableId
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getWithLinks(int $tableId)
    {
        return $this->get($tableId, [
            'contain' => [
                'CustomLinks' => [
                    'conditions' => ['CustomLinks.status' => true],
                    'CustomFields'
                ]
        ]]);
    }

    /**
     * カスタムテーブルの一覧データを取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams)
    {
        $options = array_merge([
            'type' => null
        ], $queryParams);

        $conditions = [];
        if(!is_null($options['type'])) $conditions = ['CustomTables.type' => $options['type']];

        return $this->CustomTables->find()->where($conditions);
    }

    /**
     * カスタムテーブルを新規登録する
     *
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $entity = $this->CustomTables->patchEntity($this->CustomTables->newEmptyEntity(), $postData);
        $this->CustomTables->getConnection()->begin();
        try {
            $entity = $this->CustomTables->saveOrFail($entity);
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);

            // テーブルの作成処理
            if (!$customEntriesService->createTable($entity->id)) {
                $this->CustomTables->getConnection()->rollback();
                throw new BcException(__d('baser_core', 'データベースに問題があります。エントリー保存用テーブルの生成処理に失敗しました。'));
            }
        } catch (\Throwable $e) {
            $this->CustomTables->getConnection()->rollback();
            throw $e;
        }
        $this->CustomTables->getConnection()->commit();
        return $entity;
    }

    /**
     * カスタムテーブルを編集する
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
        $this->CustomTables->getConnection()->begin();
        try {
            $oldName = $entity->name;
            if (!empty($postData['new'])) {
                foreach($postData['new'] as $new) {
                    $postData['custom_links'][] = $new;
                }
            }

            if(empty($postData['custom_links'])) {
                $postData['custom_links'] = [];
            } else {
                $no = $this->CustomTables->CustomLinks->getMax('no', [
                    'CustomLinks.custom_table_id' => $postData['id']
                ]);
                foreach($postData['custom_links'] as $key => $customLink) {
                    if(empty($customLink['no'])) {
                        $postData['custom_links'][$key]['no'] = ++$no;
                    }
                }
            }

            /** @var CustomTable $entity */
            $entity = $this->CustomTables->patchEntity($entity, $postData);
            $entity = $this->CustomTables->saveOrFail($entity);

            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);

            // テーブルのリネーム処理
            if (!$customEntriesService->renameTable($entity->id, $oldName)) {
                $this->CustomTables->getConnection()->rollback();
                throw new BcException(__d('baser_core', 'データベースに問題があります。エントリー保存用テーブルのリネーム処理に失敗しました。'));
            }

            // 関連フィードの削除されたフィールドの反映、並び順の更新を実行
            /** @var CustomLinksServiceInterface $customEntriesService */
            $customLinksService = $this->getService(CustomLinksServiceInterface::class);
            $customLinksService->updateFields(
                $entity->id,
                $entity->custom_links
            );

            // フィールドの追加処理
            $customEntriesService->addFields($entity->id, $entity->custom_links);

        } catch (\Throwable $e) {
            $this->CustomTables->getConnection()->rollback();
            throw $e;
        }
        $this->CustomTables->getConnection()->commit();
        return $entity;
    }

    /**
     * カスタムテーブルを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id)
    {
        $entity = $this->get($id);

        // カスタムコンテンツとの関連を解除
        /** @var CustomContentsServiceInterface $customContentsService */
        $customContentsService = $this->getService(CustomContentsServiceInterface::class);
        $customContentsService->unsetTable($entity->id);

        // カスタムエントリーのDBテーブルを削除
        // カスタムテーブルを削除する前にDBテーブルを削除しないとDBテーブル名が取得できずエラーとなる
        /** @var CustomEntriesService $customEntriesService */
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        $customEntriesService->dropTable($entity->id);

        return $this->CustomTables->delete($entity);
    }

    /**
     * カスタムテーブルのリストを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(array $options = [])
    {
        $query = $this->CustomTables->find('list');
        if(!empty($options['type'])) {
            $query->where(['type' => $options['type']]);
        }
        return $query->toArray();
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, array $options = []): array
    {
        if ($field === 'display_field') {
            $displayFields = [];
            if($options['id']) {
                $displayFields = $this->CustomTables->CustomLinks->find('list', [
                    'keyField' => 'name',
                    'valueField' => 'title'
                ])->where(['custom_table_id' => $options['id']])->toArray();
            }
            return array_merge(['title' => __d('baser_core', 'タイトル'), 'name' => __d('baser_core', 'スラッグ')], $displayFields);
        }
        return [];
    }

    /**
     * カスタムコンテンツIDを取得する
     * @param int $id
     * @return false|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCustomContentId(int $id)
    {
        $content = $this->CustomTables->CustomContents->find()
            ->where(['CustomContents.custom_table_id' => $id])
            ->contain(['Contents'])
            ->first();
        if($content) {
            return $content->id;
        }
        return false;
    }

}
