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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\ContentsService;
use BaserCore\Service\Admin\ContentManageServiceInterface;


/**
 * ContentManageService
 */
class ContentManageService extends ContentsService implements ContentManageServiceInterface
{

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
        // TODO: 一時措置
        $queryParams['site_id'] = 0;

        switch($queryParams['action']) {
            case 'index':
                switch($queryParams['list_type']) {
                    case 1:
                        $dataset = ['ajax_index_tree' => $this->getTreeIndex($queryParams['site_id'])];
                        break;
                    case 2:
                        $dataset = ['ajax_index_table' => $this->getTableIndex($queryParams)];
                        break;
                }
                break;
            case 'trash_index':
                $dataset = ['ajax_index_trash' => $this->getTrashIndex()];
                break;
        }
        return $dataset;
    }
}

