<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib
 * @since           baserCMS v 3.0.7
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcSite
 */
class BcSite
{

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
     * サブドメインを利用するかどうか
     * @var bool
     */
    public $useSubDomain;

    /**
     * ドメインタイプ
     *    1:サブドメイン
     *    2:別ドメイン
     * @var int
     */
    public $domainType;

    /**
     * テーマ名
     * @var string
     */
    public $theme;

    /**
     * ホスト名
     * @var string
     */
    public $host;

    /**
     * コンストラクタ
     *
     * @param string $name 名前
     * @param array $config 設定の配列
     */
    public function __construct($name, array $config)
    {
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
    protected function _setConfig(array $config)
    {
        if ($config['alias']) {
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
        $this->theme = $config['theme'];
        $this->useSubDomain = $config['use_subdomain'];
        if ($this->useSubDomain) {
            if (!empty($config['domain_type'])) {
                $this->domainType = $config['domain_type'];
            } else {
                $this->domainType = 1;
            }
        } else {
            $this->domainType = 0;
        }
        $this->host = BcSite::getHost();
    }

    /**
     * 関連するサブサイトを全て取得する
     *
     * @return BcSite[]
     */
    public static function findAll()
    {
        if (!BC_INSTALLED) {
            return [];
        }
        if (!is_null(self::$_sites)) {
            return self::$_sites;
        }
        try {
            /* @var Site $Site */
            $Site = ClassRegistry::init('Site');
        } catch (Exception $e) {
            return [];
        }
        $sites = $Site->find('all', ['recursive' => -1]);
        array_unshift($sites, $Site->getRootMain());
        self::$_sites = [];
        foreach($sites as $site) {
            self::$_sites[] = new self($site['Site']['name'], $site['Site']);
        }
        return self::$_sites;
    }

    /**
     * 設定が有効かどうかを判定
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
     *
     * @param CakeRequest $request リクエスト
     * @param BcAbstractDetector $detector
     * @return bool
     */
    public function shouldRedirects(CakeRequest $request)
    {
        if (!$this->isEnabled() || !$this->existsUrl($request)) {
            return false;
        }
        if (!$this->isEnabled() || !$this->autoRedirect) {
            return false;
        }
        $autoRedirectKey = "{$this->name}_auto_redirect";
        if (isset($request->query[$autoRedirectKey])
            && in_array($request->query[$autoRedirectKey], ['on', 'off'])) {
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
    public function existsUrl(CakeRequest $request)
    {
        $url = $this->makeUrl($request);
        if (strpos($url, '?') !== false) {
            $url = explode('?', $url)[0];
        }
        /* @var Content $Content */
        $Content = ClassRegistry::init('Content');
        if ($Content->findByUrl($url, true, false, $this->sameMainUrl, $this->useSubDomain) ||
            $Content->findByUrl($url, true, true, $this->sameMainUrl, $this->useSubDomain)) {
            return true;
        }
        return false;
    }

    /**
     * エイリアスを反映したURLを生成
     * 同一URL設定のみ利用可
     *
     * @param CakeRequest $request リクエスト
     * @return string
     */
    public function makeUrl(CakeRequest $request)
    {
        $here = $request->here(false);
        if (!$this->alias) {
            if ($here === '/index') {
                return "/";
            }
            return h($here);
        }
        if ($here === '/index') {
            return h("/{$this->alias}/");
        }
        return h("/{$this->alias}{$here}");
    }

    /**
     * メインサイトを取得
     * @return BcSite|null
     */
    public function getMain()
    {
        if (is_null($this->mainSiteId)) {
            return null;
        }
        $sites = self::findAll();
        foreach($sites as $site) {
            if ($this->mainSiteId == $site->id) {
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
    public function getPureUrl($url)
    {
        $url = preg_replace('/^\//', '', $url);
        if ($this->alias) {
            return '/' . preg_replace('/^' . preg_quote($this->alias, '/') . '\//', '', $url);
        }
        return '/' . $url;
    }

    /**
     * 初期状態に戻す
     */
    public static function flash()
    {
        self::$_sites = null;
    }

    /**
     * ホストを取得する
     *
     * @param BcSite $site
     * @return string
     */
    public function getHost()
    {
        if ($this->useSubDomain) {
            if ($this->domainType == 1) {
                return $this->alias . '.' . BcUtil::getMainDomain();
            } elseif ($this->domainType == 2) {
                return $this->alias;
            }
        }
        return BcUtil::getMainDomain();
    }

}
