<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface PageServiceInterface
 * @package BaserCore\Service
 */
interface PageServiceInterface
{

    /**
     * 固定ページを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * 固定ページをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     */
    public function getTrash($id);

    /**
     * ユーザー管理の一覧用のデータを取得
     * @param array|null $queryParams
     * @return Query
     */
    public function getIndex(array $queryParams=[]): Query;

    /**
     * 固定ページ登録
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public function create(array $postData, $options=[]);

    /**
     * 固定ページを削除する
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * ページ情報を更新する
     * @param EntityInterface $target
     * @param array $$pageData
     * @param array $options
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public function update(EntityInterface $target, array $pageData, $options = []);

    // /**
    //  * DBログ一覧を取得
    //  * @param array $queryParams
    //  * @return Query
    //  */
    // public function getIndex(array $queryParams): Query;

    /**
     * 固定ページテンプレートリストを取得する
     *
     * @param int $contentId
     * @param array|string $plugins
     * @return array
     */
    public function getPageTemplateList($contentId, $plugins);

    /**
     * ページデータをコピーする
     *
     * 固定ページテンプレートの生成処理を実行する必要がある為、
     * Content::copy() は利用しない
     *
     * @param array $postData
     * @return Page $result
     */
    public function copy($postData);
}
