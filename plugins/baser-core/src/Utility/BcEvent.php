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

namespace BaserCore\Utility;

use Cake\Event\EventManager;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * イベントユーティリティ
 */
class BcEvent
{

    /**
     * プラグインイベントを登録する
     *
     * @param string $plugin
     * @param int $priority
     */
    public static function registerPluginEvent(string $plugin, int $priority = 100)
    {
        $pluginPath = BcUtil::getPluginPath($plugin);
        // プラグインイベント登録
        $eventTargets = ['Controller', 'Model', 'View', 'Helper'];
        foreach($eventTargets as $eventTarget) {
            $eventClassName = $plugin . $eventTarget . 'EventListener';
            if (file_exists($pluginPath . 'src' . DS . 'Event' . DS . $eventClassName . '.php')) {
                $event = EventManager::instance();
                $class = '\\' . $plugin . '\\Event\\' . $eventClassName;
                $pluginEvent = new $class();
                foreach($pluginEvent->events as $key => $options) {
                    // プラグイン側で priority の設定がされてない場合に設定
                    if (is_array($options)) {
                        if (empty($options['priority'])) {
                            $options['priority'] = $priority;
                            $pluginEvent->events[$key] = $options;
                        }
                    } else {
                        unset($pluginEvent->events[$key]);
                        $pluginEvent->events[$options] = ['priority' => $priority];
                    }
                }
                $event->on($pluginEvent, null);
            }
        }
    }

}
