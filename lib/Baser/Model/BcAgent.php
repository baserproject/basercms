<?php
/**
 * BcAgent
 *
 * ユーザーエージェント
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 3.1.0-beta
 * @license			http://basercms.net/license/index.html
 */

/**
 * Class BcAgent
 */
class BcAgent {

/**
 * キャッシュ用
 * @var static[]
 */
	protected static $_agents = array();

/**
 * 名前
 * @var string
 */
	public $name;

/**
 * エイリアス
 * @var string
 */
	public $alias;

/**
 * プレフィックス
 * @var string
 */
	public $prefix;

/**
 * 自動リダイレクト
 * @var bool
 */
	public $autoRedirect;

/**
 * 自動リンク
 * @var bool
 */
	public $autoLink;

/**
 * ユーザーエージェントの判定キーワード
 * @var array
 */
	public $userAgents;

/**
 * セッションIDを付与するかどうか
 * @var bool
 */
	public $sessionId;

/**
 * 名前をキーとしてインスタンスを探す
 *
 * @param string $name 名前
 * @return BcAgent|null
 */
	public static function find($name) {
		$key = "BcAgent.{$name}";
		if (Configure::check($key)) {
			return null;
		}
		return new static($name, Configure::read($key));
	}

/**
 * 設定ファイルに存在する全てのインスタンスを返す
 *
 * @return BcAgent[]
 */
	public static function findAll() {
		if (!empty(static::$_agents)) {
			return static::$_agents;
		}

		$configs = Configure::read("BcAgent");

		foreach ($configs as $name => $config) {
			static::$_agents[] = new static($name, $config);
		}

		return static::$_agents;
	}

/**
 * URL用aliasをキーとしてインスタンスを返す
 *
 * @param string $alias URL用エイリアス
 * @return BcAgent|null
 */
	public static function findByAlias($alias) {
		$agents = static::findAll();

		foreach ($agents as $agent) {
			if ($agent->alias === $alias) {
				return $agent;
			}
		}
		return null;
	}

/**
 * 現在の環境のHTTP_USER_AGENTの値に合致するインスタンスを返す
 *
 * @return BcAgent|null
 */
	public static function findCurrent() {
		$agents = static::findAll();

		$userAgent = env('HTTP_USER_AGENT');
		if (empty($userAgent)) {
			return null;
		}

		foreach ($agents as $agent) {
			if ($agent->userAgentMatches($userAgent)) {
				return $agent;
			}
		}
		return null;
	}

/**
 * URL文字列からエイリアス文字列を取得
 *
 * @param string $url URL文字列
 * @return string|null
 */
	public static function extractAlias($url) {
		if (empty($url)) {
			return null;
		}

		$params = explode('/', $url);
		$agent = static::findByAlias($params[0]);

		if (is_null($agent)) {
			return null;
		}

		return $agent->alias;
	}

/**
 * コンストラクタ
 *
 * @param string $name 名前
 * @param array $config 設定の配列
 */
	public function __construct($name, array $config) {
		$this->name = $name;
		$config = array_merge($this->_getDefaultConfig(), $config);
		$this->_setConfig($config);
	}

/**
 * 設定
 *
 * @param array $config 設定の配列
 * @return void
 */
	protected function _setConfig(array $config) {
		$this->alias = $config['alias'];
		$this->prefix = $config['prefix'];
		$this->autoRedirect = $config['autoRedirect'];
		$this->autoLink = $config['autoLink'];
		$this->userAgents = $config['agents'];
		$this->sessionId = $config['sessionId'];
	}

/**
 * デフォルトの設定値を取得
 *
 * @return array
 */
	protected function _getDefaultConfig() {
		return array(
			'alias' => '',
			'prefix' => '',
			'autoRedirect' => true,
			'autoLink' => true,
			'userAgents' => array(),
			'sessionId' => false
		);
	}

/**
 * エージェント用の設定が有効かどうかを判定
 *
 * @return bool
 */
	public function isEnabled() {
		return (bool)Configure::read("BcApp.{$this->name}");
	}

/**
 * URLがエージェント用かどうかを判定
 *
 * @param CakeRequest $request リクエスト
 * @return bool
 */
	public function urlMatches($request) {
		if (!$this->isEnabled()) {
			return false;
		}
		$parameters = explode('/', $request->url);
		return $parameters[0] === $this->alias;
	}

/**
 * ユーザーエージェントの判定用正規表現を取得
 *
 * @return string
 */
	public function getUserAgentRegex() {
		$regex = '/' . str_replace('\|\|', '|', preg_quote(implode('||', $this->userAgents), '/')) . '/i';
		return $regex;
	}

/**
 * ユーザーエージェントがキーワードを含むかどうかを判定
 *
 * @param string $userAgent ユーザーエージェント文字列
 * @return bool
 */
	public function userAgentMatches($userAgent) {
		$regex = $this->getUserAgentRegex();
		return (bool)preg_match($regex, $userAgent);
	}

/**
 * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
 *
 * @param CakeRequest $request リクエスト
 * @return bool
 */
	public function shouldRedirects(CakeRequest $request) {
		if (!$this->isEnabled() || !$this->autoRedirect || $this->urlMatches($request)) {
			return false;
		}

		// URLによる AUTO REDIRECT 設定
		$autoRedirectKey = "{$this->prefix}_auto_redirect";

		if (isset($request->query[$autoRedirectKey])
			&& in_array($request->query[$autoRedirectKey], array('on', 'off'))) {
			CakeSession::write($autoRedirectKey, $request->query[$autoRedirectKey]);
		}

		if (isset($request->query[$this->prefix])) {
			switch($request->query[$this->prefix]) {
				case 'on':
					return true;
				case 'off':
					return false;
			}
		}

		return CakeSession::read($autoRedirectKey) !== 'off';
	}

/**
 * リクエストをリダイレクトするURLを生成
 *
 * @param CakeRequest $request リクエスト
 * @return string
 */
	public function makeRedirectUrl(CakeRequest $request) {
		$hereWithQuery = $request->here(false);
		$alias = static::extractAlias($request->url);

		if (is_null($alias)) {
			return "{$this->alias}{$hereWithQuery}";
		}

		$replacedUrl = preg_replace('/^\/' . $alias . '/', $this->alias, $hereWithQuery);

		return $replacedUrl;
	}
} 