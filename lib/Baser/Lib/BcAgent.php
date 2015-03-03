<?php
/**
 * BcAgent
 *
 * ユーザーエージェント
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

/**
 * Class BcAgent
 */
class BcAgent {

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
		if (!Configure::check($key)) {
			return null;
		}
		return new self($name, Configure::read($key));
	}

/**
 * 設定ファイルに存在する全てのインスタンスを返す
 *
 * @return BcAgent[]
 */
	public static function findAll() {
		$configs = Configure::read("BcAgent");
		$agents = array();
		foreach ($configs as $name => $config) {
			$agents[] = new self($name, $config);
		}

		return $agents;
	}

/**
 * URL用aliasをキーとしてインスタンスを返す
 *
 * @param string $alias URL用エイリアス
 * @return BcAgent|null
 */
	public static function findByAlias($alias) {
		$agents = self::findAll();

		foreach ($agents as $agent) {
			if ($agent->alias === $alias) {
				return $agent;
			}
		}
		return null;
	}

/**
 * HTTPリクエストのURLのプレフィックスに合致するインスタンスを返す
 *
 * @param CakeRequest $request URLをチェックするリクエスト
 * @return BcAgent|null
 */
	public static function findByUrl(CakeRequest $request) {
		$agents = self::findAll();

		foreach ($agents as $agent) {
			if (preg_match('/^' . $agent->alias . '\//', $request->url)) {
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
		$agents = self::findAll();

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
		$agent = self::findByAlias($params[0]);

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
		$alias = self::extractAlias($request->url);

		if (is_null($alias)) {
			return "{$this->alias}{$hereWithQuery}";
		}

		$replacedUrl = preg_replace('/^\/' . $alias . '/', $this->alias, $hereWithQuery);

		return $replacedUrl;
	}
} 