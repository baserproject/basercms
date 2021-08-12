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
     * getAdminTableConditions
     *
     * @param  array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAdminTableConditions(array $queryParams): array
    {
        $conditions = [];
        if ($queryParams['name']) {
            $conditions['OR'] = [
                'name LIKE' => '%' . $queryParams['name'] . '%',
                'title LIKE' => '%' . $queryParams['name'] . '%'
            ];
        }
        if ($queryParams['folder_id']) {
            $Contents = $this->Contents->find('all')->select(['lft', 'rght'])->where(['id' => $queryParams['folder_id']]);
            $conditions['rght <'] = $Contents->first()->rght;
            $conditions['lft >'] = $Contents->first()->lft;
        }
        if ($queryParams['author_id']) {
            $conditions['author_id'] = $queryParams['author_id'];
        }
        if ($queryParams['self_status'] !== '') {
            $conditions['self_status'] = $queryParams['self_status'];
        }
        if ($queryParams['type']) {
            $conditions['type'] = $queryParams['type'];
        }

        return $conditions;
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
        // TODO: 一時措置
        $siteId = 0;
        // $siteId = $queryParams['site_id'];

        switch($action) {
            case 'index':
                switch($listType) {
                    case 1:
                        $dataset = ['ajax_index_tree' => $this->getTreeIndex($siteId)];
                        break;
                    case 2:
                        $dataset = ['ajax_index_table' => $this->getIndex($siteId, $this->getAdminTableConditions($queryParams))];
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

