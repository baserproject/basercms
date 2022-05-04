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

use Cake\Datasource\EntityInterface;

/**
 * Interface PagesServiceInterface
 * @package BaserCore\Service
 */
interface PagesServiceInterface extends CrudBaseServiceInterface
{

    /**
     * 固定ページをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     */
    public function getTrash($id);

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
