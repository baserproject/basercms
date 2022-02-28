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
class BcEventListener extends CakeObject implements CakeEventListener
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
		parent::__construct();
		$class = get_class($this);
		$this->plugin = str_replace($this->layer . 'EventListener', '', $class);
	}

	/**
	 * implementedEvents
	 *
	 * @return array
	 */
	public function implementedEvents()
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
	 * @param string|array $action アクションを特定する為の文字列
	 * @param bool $isContainController コントローラー名を含むかどうか（初期値：true）
	 * @param bool $currentRequest 現在のリクエストかどうか（初期値：false）
	 *        ※ Controller::requestAction() を利用時に、その対象のリクエストについて判定する場合は、trueを指定する
	 * @return bool
	 */
	public function isAction($action, $isContainController = true, $currentRequest = false)
	{
		$currentAction = $this->getAction($isContainController, $currentRequest);
		if (!is_array($action)) {
			$action = [$action];
		}
		return in_array($currentAction, $action);
	}

	/**
	 * 現在のアクションを特定する文字列を取得する
	 *
	 * @param bool $isContainController コントローラー名を含むかどうか（初期値：true）
	 * @param bool $currentRequest 現在のリクエストかどうか（初期値：false）
	 *        ※ Controller::requestAction() を利用時に、その対象のリクエストについて判定する場合は、trueを指定する
	 * @return string
	 */
	public function getAction($isContainController = true, $currentRequest = false)
	{
		$request = Router::getRequest($currentRequest);
		$currentAction = Inflector::camelize($request->params['action']);
		if ($isContainController) {
			$currentAction = Inflector::camelize($request->params['controller']) . '.' . $currentAction;
		}
		return $currentAction;
	}

}
