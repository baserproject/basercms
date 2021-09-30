<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;


use Cake\ORM\Query;
use Cake\Datasource\EntityInterface;
/**
 * Interface ContentFolderServiceInterface
 * @package BaserCore\Service
 */
interface ContentFolderServiceInterface
{
    /**
     * コンテンツフォルダーを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * コンテンツフォルダーをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     */
    public function getTrash($id);

    /**
     * コンテンツフォルダー一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     */
    public function getIndex(array $queryParams=[]): Query;

    /**
     * コンテンツフォルダー登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     */
    public function create(array $postData);

    /**
     * コンテンツフォルダーを削除する
     * @param int $id
     * @return bool
     */
    public function delete($id);

}
