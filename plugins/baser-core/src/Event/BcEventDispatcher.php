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

use Cake\Event\Event;
use Cake\Event\EventManager;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
 * 存在する場合には、生成された Event を返す。
 */
class BcEventDispatcher
{

    /**
     * dispatch
     *
     * 命名規則に従ったイベント名で、イベントをディスパッチする
     *
     * @param string $name
     * @param Object $subject
     * @param array $data
     * @param array $options
     * @return boolean|Event
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function dispatch($name, $subject, $data = [], $options = [])
    {
        $options = array_merge([
            'layer' => '',
            'plugin' => method_exists($subject, 'getPlugin') ? $subject->getPlugin() : '',
            'class' => method_exists($subject, 'getName') ? $subject->getName() : ''
        ], $options);

        $eventName = '';
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

        $eventManager = EventManager::instance();
        if (!$eventManager->listeners($eventName)) {
            return false;
        }

        $event = new Event($eventName, $subject, $data);
        return $eventManager->dispatch($event);
    }

}
