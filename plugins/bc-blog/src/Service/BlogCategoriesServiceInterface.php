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

namespace BcBlog\Service;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BlogCategoriesServiceInterface
 */
interface BlogCategoriesServiceInterface
{

    /**
     * 単一レコードを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $queryParams = []): EntityInterface;

    /**
     * 一覧を取得する
     *
     * @param int $blogContentId
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(int $blogContentId, array $queryParams, $type = 'all'): Query;

    /**
     * getTreeIndex
     *
     * @param int $blogContentId
     * @param array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTreeIndex(int $blogContentId, array $queryParams): array;

    /**
     * コントロールソース取得
     *
     * @param string $field
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, array $options): mixed;

    /**
     * 新規エンティティ取得
     *
     * @param int $blogContentId
     * @param int|null $parentId 親カテゴリID（子カテゴリとして追加する場合に指定）
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $blogContentId, ?int $parentId = null): EntityInterface;

    /**
     * 新規作成
     *
     * @param int $blogContentId
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(int $blogContentId, array $postData): ?EntityInterface;

    /**
     * 更新する
     *
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface|null
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface;

    /**
     * 削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool;

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

    /**
     * IDを指定して名前リストを取得する
     *
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNamesById($ids): array;

    /**
     *ブログカテゴリーリスト取得

     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList($blogContentId, array $queryParams = []): array;

    /**
     * カテゴリの配置を移動する（並び替え・再親付け）
     *
     * 移動元の親IDは DB 上のエンティティから取得する
     *
     * @param array $origin 移動元 ['id' => int]（parentId は後方互換のため受け付けるが利用しない）
     * @param array $target 移動先 ['id' => int|null, 'parentId' => int|null]
     * @return EntityInterface|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move(array $origin, array $target);

    /**
     * ブログカテゴリのツリー構造をチェックする
     *
     * 問題がある場合にはログを出力する
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function verityTree(): bool;

    /**
     * ブログカテゴリのツリー構造をリセットする
     *
     * 全てのカテゴリをルート直下にフラット化し、lft / rght を振り直す。
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetTree(): bool;
}
