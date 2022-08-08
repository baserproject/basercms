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

namespace BaserCore\Service;

use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\Utility\BcContainerTrait;
use Cake\Utility\Inflector;

/**
 * Class SearchIndexesService
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesService implements SearchIndexesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * SearchIndexes Table
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * SearchIndexesService constructor.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->SearchIndexes = TableRegistry::getTableLocator()->get('BaserCore.SearchIndexes');
    }

    /**
     * 索引を取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->SearchIndexes->get($id);
    }

    /**
     * 検索インデックス再構築
     *
     * @param int $parentContentId 親となるコンテンツID
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reconstruct($parentContentId = null)
    {
        $Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');;
        $conditions = [
            'OR' => [
                ['Sites.status IS' => null],
                ['Sites.status' => true]
            ]];
        if ($parentContentId) {
            $parentContent = $Contents->find()->select(['lft', 'rght'])->where(['id' => $parentContentId])->first();
            $conditions = array_merge($conditions, [
                'lft >' => $parentContent->lft,
                'rght <' => $parentContent->rght
            ]);
        }
        $contents = $Contents->find()->contain(['Sites'])->where($conditions)->order('lft')->all();
        $models = [];
        $db = $this->SearchIndexes->getConnection();
        $db->begin();

        if (!$parentContentId) {
            $sql = $this->SearchIndexes->getSchema()->truncateSql($this->SearchIndexes->getConnection());
            $this->SearchIndexes->getConnection()->execute($sql[0])->execute();
        }

        $result = true;
        if ($contents) {
            foreach($contents as $content) {
                $tableName = Inflector::pluralize($content->type);
                if (isset($models[$content->type])) {
                    $table = $models[$content->type];
                } else {
                    $models[$content->type] = $table = TableRegistry::getTableLocator()->get($content->plugin . '.' . $tableName);
                }
                // データの変更はないがイベントを走らせるために setNew() を実行
                $entity = $table->find()->contain(['Contents'])->where([$tableName . '.id' => $content->entity_id])->first();
                if ($entity && $entity->setNew(true) && !$table->save($entity)) {
                    $result = false;
                }
            }
        }
        if ($result) {
            $db->commit();
        } else {
            $db->rollback();
        }
        return $result;
    }
}
