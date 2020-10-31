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

/**
 * Class UtilitiesController
 * @package BaserCore\Controller\Admin
 */
class UtilitiesController extends BcAdminAppController {
    /**
     * サーバーキャッシュを削除する
     */
    public function clear_cache() {
        // TODO 未実装
        $this->BcMessage->setError("おっと、まだ処理は実装されていませんよ！！！ \n\n/tmp/cache/ 内を削除するだけなので簡単です＾＾");
        $this->redirect($this->referer());
    }
}
