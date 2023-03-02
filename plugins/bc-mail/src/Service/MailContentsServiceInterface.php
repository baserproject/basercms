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

namespace BcMail\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * MailContentsServiceInterface
 */
interface MailContentsServiceInterface
{

    /**
     * 初期値を取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     * @checked
     */
    public function getNew();

    /**
     * メールフォーム登録
     *
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData, $options = []): ?EntityInterface;

    /**
     * メールコンテンツを更新する
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $entity, array $postData): ?EntityInterface;

    /**
     * メールフォームを削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool;

    /**
     * メールコンテンツを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $options = []);

    /**
     * メールコンテンツ一覧を取得する
     *
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(): Query;

    /**
     * リストデータ取得
     *
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getList();

    /**
     * ブログをコピーする
     *
     * @param array $postData
     * @return EntityInterface $result
     * @noTodo
     * @checked
     * @unitTest
     */
    public function copy($postData);

}
