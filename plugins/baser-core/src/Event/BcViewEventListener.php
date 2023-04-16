<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Event;

/**
 * Class BcViewEventListener
 *
 * ビューイベントリスナー
 *
 * Viewイベントにコールバック処理を登録するための継承用クラス。
 * events プロパティに配列で、イベント名を登録する。
 * イベント名についてレイヤー名は省略できる。
 * コールバック関数はイベント名より .（ドット）をアンダースコアに置き換えた上でキャメルケースに変換したものを
 * 同クラス内のメソッドとして登録する
 *
 * （例）
 * View.Dashboard.beforeRendr に対してコールバック処理を登録
 *
 * public $events = array('Dashboard.beforeRender');
 * public function dashboardBeforeRender($event) {}
 *
 */
class BcViewEventListener extends BcEventListener
{

    public $layer = 'View';

}
