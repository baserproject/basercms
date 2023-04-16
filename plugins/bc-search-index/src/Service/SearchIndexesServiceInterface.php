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

namespace BcSearchIndex\Service;

use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\ORM\Query;

/**
 * Interface SearchIndexesServiceInterface
 */
interface SearchIndexesServiceInterface
{

    /**
     * プラグインを取得する
     * 
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface;

    /**
     * 一覧データを取得
     * 
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query;

    /**
     * 検索インデックスを削除する
     * 
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id): bool;

    /**
     * 検索インデックス再構築
     *
     * @param int $parentContentId 親となるコンテンツID
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reconstruct($parentContentId = null);

    /**
     * 公開状態確認
     *
     * @param array $data
     * @return bool|int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allowPublish($data);

    /**
     * 優先度を変更する
     * 
     * @param EntityInterface $target
     * @param $priority
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changePriority(EntityInterface $target, $priority): ?EntityInterface;

    /**
     * 一括処理
     * 
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool;

}
