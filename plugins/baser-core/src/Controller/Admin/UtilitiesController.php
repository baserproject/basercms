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

namespace BaserCore\Controller\Admin;
use BaserCore\Utility\BcUtil;
/**
 * Class UtilitiesController
 * @package BaserCore\Controller\Admin
 */
class UtilitiesController extends BcAdminAppController
{
    /**
     * サーバーキャッシュを削除する
     */
    public function clear_cache()
    {
        $this->_checkReferer();
        BcUtil::clearAllCache();
        $this->BcMessage->setInfo(__d('baser', 'サーバーキャッシュを削除しました。'));
        $this->redirect($this->referer());
    }

    /**
     * 検索ボックスの表示状態を保存する
     *
     * @param string $key キー
     * @param mixed $open 1 Or ''
     * @return void
     */
    public function ajax_save_search_box($key, $open = '')
    {
        $this->autoRender = false;
        $this->request->getSession()->write('BcApp.adminSearchOpened.' . $key, $open);
        echo true;
    }

}
