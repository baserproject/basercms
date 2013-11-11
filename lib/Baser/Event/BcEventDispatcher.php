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
 * イベントディスパッチャー
 *
 * イベントのディスパッチ処理を簡素化する。
 * イベント名は命名規則にしたがって決定される。
 * 
 * Layer.Plugin.Class.eventName
 * 
 * 登録されたイベントリスナーが存在しない場合には、falseを を返す。
 * 存在する場合には、生成された CakeEvent を返す。
 */

class BcEventDispatcher extends Object {
	
/**
 * dispatch
 * 
 * 命名規則に従ったイベント名で、イベントをディスパッチする
 * 
 * @param string $name
 * @param Object $subject
 * @param array $params
 * @param array $options
 * @return boolean|\CakeEvent
 */
	public static function dispatch($name, $subject, $params = array(), $options = array()) {
		
		$options = array_merge(array(
			'modParams' => 0,
			'layer'		=> '',
			'plugin'	=> $subject->plugin,
			'class'		=> $subject->name
		), $options);
		extract($options);		
		
		if($layer && !preg_match('/^' . $layer . './', $name)) {
			$evnetName = $layer;
			if($plugin) {
				$evnetName .= '.' . $plugin;
			}
			if($class) {
				$evnetName .= '.' . $class;
			}
			$evnetName .= '.' . $name;
		}
		
		$EventManager = CakeEventManager::instance();
		if(!$EventManager->listeners($evnetName)) {
			return false;
		}
		
		$event = new CakeEvent($evnetName, $subject, $params);
		$event->modParams = $modParams;
		$EventManager->dispatch($event);
		
		return $event;
		
	}
	
}