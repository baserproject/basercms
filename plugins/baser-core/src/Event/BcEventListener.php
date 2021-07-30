<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Event;

use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Class BcEventListener
 *
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
class BcEventListener implements EventListenerInterface
{

    /**
     * 登録イベント
     *
     * @var array
     */
    public $events = [];

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
    public function __construct()
    {
        $classArray = explode('\\', get_class($this));
        $class = $classArray[count($classArray) -1];
        $this->plugin = str_replace($this->layer . 'EventListener', '', $class);
    }

    /**
     * implementedEvents
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        $events = [];
        if ($this->events) {
            foreach($this->events as $key => $registerEvent) {
                $options = [];
                if (is_array($registerEvent)) {
                    $options = $registerEvent;
                    $registerEvent = $key;
                }
                $eventName = $this->layer . '.' . $registerEvent;
                if (strpos($registerEvent, '.') !== false) {
                    $aryRegisterEvent = explode('.', $registerEvent);
                    $registerEvent = Inflector::variable(implode('_', $aryRegisterEvent));
                }
                if ($options) {
                    $options = array_merge(['callable' => $registerEvent], $options);
                } else {
                    $options = ['callable' => $registerEvent];
                }
                $events[$eventName] = $options;
            }
        }
        return $events;
    }

    /**
     * 指定した文字列が現在のアクションとしてみなされるかどうか判定する
     *
     * コントローラー名、アクション名をキャメルケースに変換する前提で、ドットで結合した文字列とする
     * （例）Users.AdminIndex
     *
     * @param string $action アクションを特定する為の文字列
     * @param bool $isContainController コントローラー名を含むかどうか（初期値：true）
     * @return bool
     */
    public function isAction($action, $isContainController = true)
    {
        $currentAction = $this->getAction($isContainController);
        if (!is_array($action)) {
            $action = [$action];
        }
        return in_array($currentAction, $action);
    }

    /**
     * 現在のアクションを特定する文字列を取得する
     *
     * @param bool $isContainController コントローラー名を含むかどうか（初期値：true）
     * @return string
     */
    public function getAction($isContainController = true)
    {
        $request = Router::getRequest();
        $currentAction = Inflector::camelize($request->getParams('action'));
        if ($isContainController) {
            $currentAction = Inflector::camelize($request->getParams('controller')) . '.' . $currentAction;
        }
        return $currentAction;
    }

}
