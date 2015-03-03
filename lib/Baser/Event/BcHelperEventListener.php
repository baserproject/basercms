<?php
/**
 * BcHelperEventListener
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * ヘルパーイベントリスナー
 *
 * Helperイベントにコールバック処理を登録するための継承用クラス。
 * events プロパティに配列で、イベント名を登録する。
 * イベント名についてレイヤー名は省略できる。
 * コールバック関数はイベント名より .（ドット）をアンダースコアに置き換えた上でキャメルケースに変換したものを
 * 同クラス内のメソッドとして登録する
 * 
 * （例）
 * Helper.Form.beforeCreate に対してコールバック処理を登録
 * 
 * public $events = array('Form.beforeCreate');
 * public function formBeforeCreate($event) {}
 * 
 */
class BcHelperEventListener extends BcEventListener {

/**
 * レイヤー名
 * 
 * @var string
 */
	public $layer = 'Helper';

}
