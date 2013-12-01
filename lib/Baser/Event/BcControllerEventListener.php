<?php
/** 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 3.0.0
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('BcEventListener', 'Event');

/**
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
class BcControllerEventListener extends BcEventListener {

/**
 * レイヤー名
 * 
 * @var string
 */
	public $layer = 'Controller';
	
}