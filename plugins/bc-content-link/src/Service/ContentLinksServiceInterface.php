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

namespace BcContentLink\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;

/**
 * ContentLinksServiceInterface
 */
interface ContentLinksServiceInterface
{

    /**
     * 単一データ取得
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @checked
     */
    public function get(int $id, array $options = []);

    /**
     * 新規登録
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @checked
     */
    public function create(array $postData);

    /**
     * リンクを更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @checked
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface;

    /**
     * リンクをを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id): bool;
}
