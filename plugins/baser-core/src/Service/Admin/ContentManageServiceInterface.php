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

namespace BaserCore\Service\Admin;

use Cake\ORM\Query;

use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
/**
 * Interface PluginManageServiceInterface
 * @package BaserCore\Service
 */
interface ContentManageServiceInterface extends ContentsServiceInterface
{
    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo ();

    /**
     * getAdminTableConditions
     *
     * @param  array $searchData
     * @return array
     */
    public function getAdminTableConditions($searchData): array;

    /**
     * リクエストに応じてajax処理時に必要なIndexとテンプレートを取得する
     *
     * @param  array $searchData
     * @return array
     */
    public function getAdminAjaxIndex(array $searchData): array;
}
