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

namespace BaserCore\View\Helper;

use BaserCore\Service\SiteConfigService;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcAdminSiteConfigHelper
 */
class BcAdminSiteConfigHelper extends BcSiteConfigHelper
{
    /**
     * .env が書き込み可能かどうか
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isWritableEnv()
    {
        return $this->SiteConfigService->isWritableEnv();
    }

    /**
     * 管理画面テーマリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAdminThemeList()
    {
        return BcUtil::getAdminThemeList();
    }

    /**
     * ウィジェットエリアリストを取得
     * @return array
     * @checked
     * @unitTest
     */
    public function getWidgetAreaList()
    {
        // TODO 未実装のため代替措置
        // >>>
        //$this->BcAdminForm->getControlSource('WidgetArea.id'), 'empty' => __d('baser', 'なし')]
        // ---
        return [];
        // <<<
    }

    /**
     * エディタリストを取得
     * @return array|false[]|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEditorList()
    {
        return Configure::read('BcApp.editors');
    }

    /**
     * メールエンコードリストを取得
     * @return array|false[]|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMailEncodeList()
    {
        return Configure::read('BcEncode.mail');
    }

    /**
     * アプリケーションモードリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getModeList()
    {
        return $this->SiteConfigService->getModeList();
    }

}
