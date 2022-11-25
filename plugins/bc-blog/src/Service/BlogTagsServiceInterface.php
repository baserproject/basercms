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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcBlog\Model\Entity\BlogTag;

/**
 * BlogTagsServiceInterface
 */
interface BlogTagsServiceInterface
{
    /**
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew();

    /**
     * ブログタグ作成
     *
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData);

    /**
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id);

    /**
     * ブログタグ一覧を取得
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams);

    /**
     * @param BlogTag $blogTag
     * @param $postData
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(BlogTag $blogTag, $postData);

    /**
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id);

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitlesById(array $ids): array;

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool;

}
