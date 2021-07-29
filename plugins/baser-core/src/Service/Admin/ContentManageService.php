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

use BaserCore\Service\ContentsService;

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

}

