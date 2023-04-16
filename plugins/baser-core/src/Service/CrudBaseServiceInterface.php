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

use Cake\ORM\Query;
use Cake\Datasource\EntityInterface;

/**
 * Interface CrudBaseServiceInterface
 */
interface CrudBaseServiceInterface
{

    /**
     * 新しいデータの初期値を取得する
     * 
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface;

    /**
     * 単一データを取得する
     * 
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id): EntityInterface;

    /**
     * 複数データを取得
     * 
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query;

    /**
     * リストデータを取得（コントロールソースに利用）
     * 
     * @param array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array;

    /**
     * 新規登録する
     * 
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData): ?EntityInterface;

    /**
     * 編集する
     * 
     * @param EntityInterface $target
     * @param array $postData
     * @return mixed
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface;

    /**
     * 削除する
     * 
     * @param int $id
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool;

}
