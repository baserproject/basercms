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

use BaserCore\Model\Entity\Page;
use Cake\Datasource\EntityInterface;

/**
 * Interface PagesServiceInterface
 */
interface PagesServiceInterface extends CrudBaseServiceInterface
{

    /**
     * 固定ページをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getTrash($id);

    /**
     * 固定ページテンプレートリストを取得する
     *
     * @param int $contentId
     * @param array|string $plugins
     * @return array
     * @checked
     * @unitTest
     * @noTodo
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
     * @checked
     * @unitTest
     * @noTodo
     */
    public function copy($postData);

    /**
     * 物理削除
     * @param int $id
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function delete(int $id): bool;

}
