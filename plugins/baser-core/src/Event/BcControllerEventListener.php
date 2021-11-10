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

namespace BaserCore\Event;

use BaserCore\Utility\BcUtil;
use Cake\Controller\Controller;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcControllerEventListener
 *
 * コントローラーイベントリスナー
 *
 * Controllerイベントにコールバック処理を登録するための継承用クラス。
 * events プロパティに配列で、イベント名を登録する。
 * イベント名についてレイヤー名は省略できる。
 * コールバック関数はイベント名より .（ドット）をアンダースコアに置き換えた上でキャメルケースに変換したものを
 * 同クラス内のメソッドとして登録する
 *
 * （例）
 * Controller.Dashboard.beforeRendr に対してコールバック処理を登録
 *
 * public $events = array('Dashboard.beforeRender');
 * public function dashboardBeforeRender($event) {}
 *
 */
class BcControllerEventListener extends BcEventListener
{

    /**
     * レイヤー名
     *
     * @var string
     */
    public $layer = 'Controller';

    /**
     * 管理システムの現在のサイトをセットする
     *
     * @param Controller $controller
     * @param $siteId
     * @return bool
     * @unitTest
     * @noTodo
     * @checked
     */
    public function setAdminCurrentSite(Controller $controller, $siteId)
    {
        if (!BcUtil::isAdminSystem()) {
            return false;
        }
        $controller->setRequest($controller->getRequest()->withParam('site_id', $siteId));
        return true;
    }

    /**
     * コントローラーにヘルパーを追加する
     *
     * @param Controller $controller
     * @param string|array $helper
     * @unitTest
     * @noTodo
     * @checked
     */
    public function addHelper(Controller $controller, $helper)
    {
        $builder = $controller->viewBuilder();
        if (is_array($helper)) {
            $builder->setHelpers($helper);
        } else {
            $builder->addHelper($helper);
        }
    }

}
