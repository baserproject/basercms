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

/**
 * イベントリスナー
 *
 * イベントにコールバック処理を登録するための継承用クラス。
 * events プロパティに配列で、イベント名を登録する。
 * イベント名についてレイヤー名は省略できる。
 * コールバック関数はイベント名より .（ドット）をアンダースコアに置き換えた上でキャメルケースに変換したものを
 * 同クラス内のメソッドとして登録する
 * 
 * （例）
 * View.beforeRendr に対してコールバック処理を登録
 * 
 * public $events = array('beforeRender');
 * public function beforeRender($event) {}
 * 
 */
class BcEventListener extends Object implements CakeEventListener {
/**
 * 登録イベント
 * 
 * @var array
 */
	public $events = array();
	
/**
 * レイヤー名
 * 
 * @var string
 */
	public $layer = '';
	
/**
 * implementedEvents
 * 
 * @return array
 */
    public function implementedEvents() {

		$events = array();
		if($this->events) {
			foreach($this->events as $registerEvent) {
				
				$eventName = $this->layer . '.' . $registerEvent;
				if(strpos($registerEvent, '.') !== false) {
					$aryRegisterEvent = explode('.', $registerEvent);
					$registerEvent = Inflector::variable(implode('_', $aryRegisterEvent));
				}
				$events[$eventName] = array('callable' => $registerEvent);
				
			}
		}
		
        return $events;
		
    }

}