<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib
 * @since			baserCMS v 3.0.7
 * @license			http://basercms.net/license/index.html
 */

/**
 * Class BcSite
 */
class BcSite {

/**
 * サブサイトリスト
 *
 * @var null
 */
	protected static $_sites = null;

/**
 * サイトID
 * @var int
 */
 	public $id;

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
 * 言語
 * @var string
 */
	public $lang;
	
/**
 * デバイス
 * @var string
 */
	public $device;

/**
 * 同一URL
 * @var bool
 */
	public $sameMainUrl;

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
 * 利用可否
 * @var bool
 */
	public $enabled;

/**
 * メインサイトID
 * @var int
 */
	public $mainSiteId;

/**
 * コンストラクタ
 *
 * @param string $name 名前
 * @param array $config 設定の配列
 */
	public function __construct($name, array $config) {
		$this->name = $name;
		$this->_setConfig($config);
		$this->_Site = ClassRegistry::init('Site');
	}

/**
 * 設定
 *
 * @param array $config 設定の配列
 * @return void
 */
	protected function _setConfig(array $config) {
		if($config['alias']) {
			$this->alias = $config['alias'];
		} else {
			$this->alias = $config['name'];
		}
		$this->enabled = $config['status'];
		$this->id = $config['id'];
		$this->device = $config['device'];
		$this->lang = $config['lang'];
		$this->sameMainUrl = $config['same_main_url'];
		$this->autoRedirect = $config['auto_redirect'];
		$this->autoLink = $config['auto_link'];
		$this->mainSiteId = $config['main_site_id'];
	}

/**
 * URLからサブサイトを取得する
 *
 * @param bool $direct
 * @return BcSite|null
 */
	public static function findCurrent($direct = true) {
		$request = new CakeRequest();
		$url = $request->url;
		$sites = self::findAll();
		if (!$sites) {
			return null;
		}
		$url = preg_replace('/^\//', '', $url);
		$currentSite = null;
		foreach($sites as $site) {
			if($site->alias) {
				$regex = '/^' . preg_quote($site->alias, '/') . '\//';
				if (preg_match($regex, $url)) {
					$currentSite = $site;
					break;
				}
			}
		}
		if(!$currentSite) {
			$currentSite = $sites[0];
		}
		if(!$direct) {
			$subSite = self::findCurrentSub(true);
			if($subSite) {
				$currentSite = $subSite;
			}
		}
		return $currentSite;
	}

/**
 * 現在のサイトに関連するメインサイトを取得
 * 
 * @return BcSite|null
 */
	public static function findCurrentMain() {
		$currentSite = self::findCurrent();
		$sites = self::findAll();
		$mainSite = null;
		if(!$sites) {
			return null;
		}
		foreach($sites as $site) {
			if($currentSite->mainSiteId == $site->id) {
				return $site;
			}
		}
		return null;
	}
	
/**
 * 現在のサイトとユーザーエージェントに関連するサブサイトを取得する
 *
 * @param BcAbstractDetector $detector
 * @param bool $sameMainUrl
 * @return BcSite|null
 */
	public static function findCurrentSub($sameMainUrl = false, BcAgent $agent = null, $lang = null) {
		$currentSite = self::findCurrent();
		$sites = self::findAll();
		$subSite = null;

		if($lang) {
			$lang = BcLang::findCurrent();	
		}
		if(!$agent) {
			$agent = BcAgent::findCurrent();	
		}
		
		
		// 言語の一致するサブサイト候補に絞り込む
		$subSites = [];
		if($lang) {
			foreach($sites as $site) {
				if (!$sameMainUrl || ($sameMainUrl && $site->sameMainUrl)) {
					if($site->lang == $lang->name && $currentSite->id == $site->mainSiteId) {
						$subSites[] = $site;
						break;
					}
				}
			}
		}
		if(!$subSites) {
			$subSites = $sites;
		}
		if($agent) {
			foreach($subSites as $subSite) {
				if (!$sameMainUrl || ($sameMainUrl && $subSite->sameMainUrl)) {
					if($subSite->device == $agent->name && $currentSite->id == $subSite->mainSiteId) {
						return $subSite;
					}
				}
			}
		}
		return null;
	}

/**
 * 関連するサブサイトを全て取得する
 *
 * @return BcSite[]
 */
	public static function findAll() {
		if(!BC_INSTALLED) {
			return [];
		}
		if(!is_null(self::$_sites)) {
			return self::$_sites;
		}
		$Site = ClassRegistry::init('Site');
		$sites = $Site->find('all', ['recursive' => -1]);
		array_unshift($sites, $Site->getRootMain());
		self::$_sites = [];
		foreach ($sites as $site) {
			self::$_sites[] = new self($site['Site']['name'], $site['Site']);
		}
		return self::$_sites;
	}

/**
 * 設定が有効かどうかを判定
 *
 * @return bool
 */
	public function isEnabled() {
		return $this->enabled;
	}

/**
 * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
 *
 * @param CakeRequest $request リクエスト
 * @param BcAbstractDetector $detector
 * @return bool
 */
	public function shouldRedirects(CakeRequest $request) {
		if(!$this->isEnabled() || !$this->existsUrl($request)) {
			return false;
		}
		if (!$this->isEnabled() || !$this->autoRedirect) {
			return false;
		}
		$autoRedirectKey = "{$this->name}_auto_redirect";
		if (isset($request->query[$autoRedirectKey])
			&& in_array($request->query[$autoRedirectKey], array('on', 'off'))) {
			CakeSession::write($autoRedirectKey, $request->query[$autoRedirectKey]);
		}
		if (isset($request->query[$this->name])) {
			switch($request->query[$this->name]) {
				case 'on':
					return true;
				case 'off':
					return false;
			}
		}
		return CakeSession::read($autoRedirectKey) !== 'off';
	}

/**
 * URLが存在するか確認
 *
 * @param CakeRequest $request
 * @return bool
 */
	public function existsUrl(CakeRequest $request) {
		$url = $request->base . $this->makeUrl($request);
		$Content = ClassRegistry::init('Content');
		return $Content->existsPublishUrl($url);
	}

/**
 * エイリアスを反映したURLを生成
 *
 * @param CakeRequest $request リクエスト
 * @return string
 */
	public function makeUrl(CakeRequest $request) {
		$here = $request->here(false);
		if($this->alias) {
			return "/{$this->alias}{$here}";
		} else {
			return $here;
		}
	}

/**
 * メインサイトを取得
 * @return BcSite|null
 */
	public function getMain() {
		if(is_null($this->mainSiteId)) {
			return null;
		}
		$sites = self::findAll();
		foreach($sites as $site) {
			if($this->mainSiteId == $site->id) {
				return $site;
			}
		}
		return null;
	}

/**
 * エイリアスを除外したURLを取得
 * 
 * @param string $url
 * @return mixed|string
 */
	public function getPureUrl($url) {
		$url = preg_replace('/^\//', '',  $url);
		if($this->alias) {
			return '/' . preg_replace('/^' . preg_quote($this->alias, '/') . '\//', '', $url);
		}
		return '/' . $url;
	}

}