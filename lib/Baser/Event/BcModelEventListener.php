<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Event
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * Class BcModelEventListener
 *
 * モデルイベントリスナー
 *
 * Modelイベントにコールバック処理を登録するための継承用クラス。
 * events プロパティに配列で、イベント名を登録する。
 * イベント名についてレイヤー名は省略できる。
 * コールバック関数はイベント名より .（ドット）をアンダースコアに置き換えた上でキャメルケースに変換したものを
 * 同クラス内のメソッドとして登録する
 *
 * （例）
 * Model.User.beforeFind に対してコールバック処理を登録
 *
 * public $events = array('User.beforeFind');
 * public function userBeforeFind($event) {}
 *
 */
class BcModelEventListener extends BcEventListener
{

	/**
	 * レイヤー名
	 *
	 * @var string
	 */
	public $layer = 'Model';

}
