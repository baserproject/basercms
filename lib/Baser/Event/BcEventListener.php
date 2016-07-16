<?php
/**
 * BcEventListener
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
 * プラグイン名
 * 
 * @var string 
 */
	public $plugin = '';
/**
 * コンストラクタ
 */
	public function __construct() {
		parent::__construct();
		$class = get_class($this);
		$this->plugin = str_replace($this->layer . 'EventListener', '', $class);
	}
/**
 * implementedEvents
 * 
 * @return array
 */
	public function implementedEvents() {
		
		$events = array();
		if ($this->events) {
			foreach ($this->events as $key => $registerEvent) {
				$options = array();
				if(is_array($registerEvent)) {
					$options = $registerEvent;
					$registerEvent = $key;
				}
				$eventName = $this->layer . '.' . $registerEvent;
				if (strpos($registerEvent, '.') !== false) {
					$aryRegisterEvent = explode('.', $registerEvent);
					$registerEvent = Inflector::variable(implode('_', $aryRegisterEvent));
				}
				if($options) {
					$options = array_merge(array('callable' => $registerEvent), $options);
				} else {
					$options = array('callable' => $registerEvent);
				}
				$events[$eventName] = $options;
			}
		}

		return $events;
		
	}

}
