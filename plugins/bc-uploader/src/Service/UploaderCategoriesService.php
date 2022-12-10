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

namespace BcUploader\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BcUploader\Model\Table\UploaderCategoriesTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * UploadCategoriesService
 *
 * @property UploaderCategoriesTable $UploaderCategories
 */
class UploaderCategoriesService implements UploaderCategoriesServiceInterface
{

    /**
     * Constructor
     *
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->UploaderCategories = TableRegistry::getTableLocator()->get('BcUploader.UploaderCategories');
    }

    /**
     * 単一データ取得
     *
     * @checked
     * @noTodo
     */
    public function get(int $id)
    {
        return $this->UploaderCategories->get($id);
    }

    /**
     * 一覧データ取得
     *
     * @checked
     * @noTodo
     */
    public function getIndex(array $queryParams = [])
    {
        return $this->UploaderCategories->find()->contain(['UploaderFiles']);
    }

    /**
     * リスト取得
     *
     * @checked
     * @noTodo
     */
    public function getList()
    {
        return $this->UploaderCategories->find('list')->toArray();
    }

    /**
     * 初期データ取得
     *
     * @checked
     * @noTodo
     */
    public function getNew()
    {
        return $this->UploaderCategories->newEmptyEntity();
    }

    /**
     * 作成
     *
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        $entity = $this->UploaderCategories->patchEntity($this->UploaderCategories->newEmptyEntity(), $postData);
        return $this->UploaderCategories->saveOrFail($entity);
    }

    /**
     * 編集
     *
     * @checked
     * @noTodo
     */
    public function update(EntityInterface $entity, array $postData)
    {
        $entity = $this->UploaderCategories->patchEntity($entity, $postData);
        return $this->UploaderCategories->saveOrFail($entity);
    }

    /**
     * 削除
     *
     * @checked
     * @noTodo
     */
    public function delete(int $id)
    {
        $entity = $this->get($id);
        return $this->UploaderCategories->delete($entity);
    }

    /**
     * コピー
     *
     * @checked
     * @noTodo
     */
    public function copy(int $id)
    {
        return $this->UploaderCategories->copy($id);
    }

    /**
     * 一括処理
     *
     * @checked
     * @noTodo
     */
    public function batch(string $method, array $ids)
    {
        if (!$ids) return true;
        $db = $this->UploaderCategories->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     */
    public function getTitlesById(array $ids): array
    {
        return $this->UploaderCategories->find('list')->select(['id', 'name'])->where(['id IN' => $ids])->toArray();
    }

}
