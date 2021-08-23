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

use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\ContentsService;


/**
 * ContentManageService
 */
class ContentManageService extends ContentsService implements ContentManageServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo ()
    {
        $sites = $this->Sites->getPublishedAll();
        $contentsInfo = [];
        foreach($sites as $key => $site) {
            $contentsInfo[$key]['published'] = $this->Contents->find()
                    ->where(['site_id' => $site->id, 'status' => true])
                    ->count();
            $contentsInfo[$key]['unpublished'] = $this->Contents->find()
                    ->where(['site_id' => $site->id, 'status' => false])
                    ->count();
            $contentsInfo[$key]['total'] = $contentsInfo[$key]['published'] + $contentsInfo[$key]['unpublished'];
            $contentsInfo[$key]['display_name'] = $site->display_name;
        }
        return $contentsInfo;
    }

    /**
     * リクエストに応じてajax処理時に必要なIndexとテンプレートを取得する
     *
     * @param  array $queryParams
     * @param  int $listType
     * @return array
     * @checked
     * @unitTest
     */
    public function getAdminAjaxIndex(array $queryParams): array
    {
        $dataset = [];
        $action = $queryParams['action'];
        $listType = $queryParams['list_type'];
        unset($queryParams['action'], $queryParams['list_type'], $queryParams['sort'], $queryParams['direction']);

        if ($listType != 2) {
            // thread形式の場合不要な条件を除外
            unset($queryParams['folder_id'], $queryParams['open']);
            if (isset($queryParams['self_status'])) {
                $queryParams['self_status'] = $queryParams['self_status'] ? (bool) $queryParams['self_status'] : false;
            }
        }
        switch($action) {
            case 'index':
                switch($listType) {
                    case 1:
                        $dataset = ['index_tree' => $this->getTreeIndex($queryParams)];
                        break;
                    case 2:
                        $dataset = ['index_table' => $this->getTableIndex($queryParams)];
                        break;
                }
                break;
            case 'trash_index':
                $dataset = ['index_trash' => $this->getTrashIndex($queryParams)];
                break;
        }
        return $dataset;
    }

    /**
     * 登録されているタイプの一覧を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTypes(): array
    {
        return $this->Contents->getTypes();
    }

    /**
     * コンテンツの作成者一覧を取得
     * @return array
     */
    public function getAuthors(): array
    {
        $users = $this->getService(UsersServiceInterface::class);
        return $users->getList();
    }

}

