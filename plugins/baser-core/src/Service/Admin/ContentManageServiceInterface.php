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
     * リクエストに応じてajax処理時に必要なIndexとテンプレートを取得する
     *
     * @param  array $queryParams
     * @return array
     */
    public function getAdminIndex(array $queryParams): array;

    /**
     * 登録されているタイプの一覧を取得する
     * @return array
     */
    public function getTypes(): array;

}
