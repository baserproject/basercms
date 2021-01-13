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

/**
 * Class BcEventDispatcher
 *
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
class BcEventDispatcher extends CakeObject
{

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
	public static function dispatch($name, $subject, $params = [], $options = [])
	{
		$options = array_merge([
			'modParams' => 0,
			'layer' => '',
			'plugin' => $subject->plugin,
			'class' => $subject->name
		], $options);
		extract($options);

		if ($options['layer'] && !preg_match('/^' . $options['layer'] . './', $name)) {
			$eventName = $options['layer'];
			if ($options['plugin']) {
				$eventName .= '.' . $options['plugin'];
			}
			if ($options['class']) {
				$eventName .= '.' . $options['class'];
			}
			$eventName .= '.' . $name;
		}

		$EventManager = CakeEventManager::instance();
		if (!$EventManager->listeners($eventName)) {
			return false;
		}

		$event = new CakeEvent($eventName, $subject, $params);
		$event->modParams = $options['modParams'];
		$EventManager->dispatch($event);

		return $event;
	}

}
