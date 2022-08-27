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

namespace BcSearchIndex\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Error\BcException;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcSearchIndexManagerBehavior
 *
 * 検索インデックス管理ビヘイビア
 *
 * @package Baser.Model.Behavior
 */
class BcSearchIndexManagerBehavior extends Behavior
{

    /**
     * 無視状態かどうか
     * @var bool
     */
    private $isExcluded = false;

    /**
     * 除外状態として設定する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setExcluded()
    {
        $this->isExcluded = true;
    }

    /**
     * 除外状態の設定を解除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unsetExcluded()
    {
        $this->isExcluded = false;
    }

    /**
     * 除外状態を確認する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isExcluded()
    {
        return $this->isExcluded;
    }

    /**
     * initialize
     * @param  array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->table = $this->table();
        if (!method_exists($this->table, 'createSearchIndex')) {
            throw new BcException(get_class($this->table) . "::createSearchIndex()が実装されてません");
        }
        /** @var \BaserCore\Model\Table\ContentsTable $Contents  */
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        /** @var \BcSearchIndex\Model\Table\SearchIndexesTable $SearchIndexes  */
        $this->SearchIndexes = TableRegistry::getTableLocator()->get('BcSearchIndex.SearchIndexes');
        /** @var \BaserCore\Model\Table\SiteConfigsTable $SiteConfigs  */
        $this->SiteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
    }

    /**
     * afterSave
     *
     * @param  EventInterface $event
     * @param  EntityInterface $entity
     * @param  ArrayObject $options
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!$this->isExcluded) {
            $this->saveSearchIndex($this->table->createSearchIndex($entity));
        } else {
            $this->deleteSearchIndex($entity->id);
        }
    }

    /**
     * After Delete
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->deleteSearchIndex($entity->id);
    }

    /**
     * 検索インデクスデータを登録する
     *
     * 検索インデクスデータを次のように作成して引き渡す
     *
     * ['SearchIndex' => [
     *        'type' => 'コンテンツのタイプ',
     *        'model_id' => 'モデルでのID',
     *        'content_id' => 'コンテンツID',
     *        'site_id' => 'サブサイトID',
     *        'content_filter_id' => 'フィルターID' // カテゴリIDなど
     *        'category' => 'カテゴリ名',
     *        'title' => 'コンテンツタイトル', // 検索対象
     *        'detail' => 'コンテンツ内容', // 検索対象
     *        'url' => 'URL',
     *        'status' => '公開ステータス',
     *        'publish_begin' => '公開開始日',
     *        'publish_end' => '公開終了日'
     * ]]
     *
     * @param array $searchIndex
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveSearchIndex($searchIndex)
    {
        if (!$searchIndex) {
            return false;
        }

        if (!empty($searchIndex['content_id'])) {
            $content = $this->Contents->find()->select(['lft', 'rght'])->where(['id' => $searchIndex['content_id']])->first();
            $searchIndex['lft'] = $content->lft;
            $searchIndex['rght'] = $content->rght;
        } else {
            $searchIndex['lft'] = 0;
            $searchIndex['rght'] = 0;
        }
        $searchIndex['model'] = Inflector::classify($this->table->getAlias());
        // タグ、空白を除外
        $searchIndex['detail'] = str_replace(["\r\n", "\r", "\n", "\t", "\s"], '', trim(strip_tags($searchIndex['detail'])));

        // 検索用データとして保存
        $before = false;
        if (!empty($searchIndex['model_id'])) {
            $before = $this->SearchIndexes->find()
                ->select(['id', 'content_id'])
                ->where([
                    'model' => $searchIndex['model'],
                    'model_id' => $searchIndex['model_id']
                ])->first();
        }
        if ($before) {
            $searchIndex['id'] = $before->id;
            $searchIndex = $this->SearchIndexes->patchEntity($before, $searchIndex);
        } else {
            if (empty($searchIndex['priority'])) {
                $searchIndex['priority'] = '0.5';
            }
            $searchIndex = $this->SearchIndexes->newEntity($searchIndex);
        }
        $result = $this->SearchIndexes->save($searchIndex);

        // カテゴリを site_configsに保存
        if ($result) {
            return $this->updateSearchIndexMeta();
        }

        return $result;
    }

    /**
     * コンテンツデータを削除する
     *
     * @param string $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteSearchIndex($id)
    {
        if ($this->SearchIndexes->deleteAll(['model' => Inflector::classify($this->table->getAlias()), 'model_id' => $id])) {
            return $this->updateSearchIndexMeta();
        }
    }

    /**
     * コンテンツメタ情報を更新する
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateSearchIndexMeta()
    {
        $contentTypes = [];
        $searchIndexes = $this->SearchIndexes->find()->select('type')->group('type')->where(['status' => true]);
        foreach($searchIndexes as $searchIndex) {
            if ($searchIndex->type) {
                $contentTypes[$searchIndex->type] = $searchIndex->type;
            }
        }
        return $this->SiteConfigs->saveValue('content_types', BcUtil::serialize($contentTypes));
    }

}
