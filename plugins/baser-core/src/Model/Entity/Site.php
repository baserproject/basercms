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

namespace BaserCore\Model\Entity;

use BaserCore\Utility\BcUtil;
use Cake\Http\ServerRequest;
use Cake\I18n\Time as TimeAlias;
use Cake\ORM\Entity as EntityAlias;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Site
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property int $main_site_id
 * @property string $display_name
 * @property string $title
 * @property string $alias
 * @property string $theme
 * @property bool $status
 * @property string $keyword
 * @property string $description
 * @property bool $relate_main_site
 * @property string $device
 * @property string $lang
 * @property bool $same_main_url
 * @property bool $auto_redirect
 * @property bool $auto_link
 * @property bool $use_subdomain
 * @property int $domain_type
 * @property TimeAlias $created
 * @property TimeAlias $modified
 */
class Site extends EntityAlias
{

    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * ドメインタイプを取得
     * @return int|null
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getDomainType()
    {
        if ($this->use_subdomain) {
            if (empty($this->_fields['domain_type'])) {
                return 1;
            }
        }
        return $this->_fields['domain_type'];
    }

    /**
     * エイリアスを取得
     * @return int|null
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getAlias()
    {
        if (empty($this->_fields['alias'])) {
            return $this->name;
        }
        return $this->_fields['alias'];
    }

    /**
     * ホストを取得する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getHost()
    {
        if ($this->use_subdomain) {
            if ($this->domain_type === 1) {
                return $this->alias . '.' . BcUtil::getMainDomain();
            } elseif ($this->domain_type === 2) {
                return $this->alias;
            }
        }
        return BcUtil::getMainDomain();
    }

    /**
     * メインサイトを取得
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMain()
    {
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        return $sites->getMain($this->id);
    }

    /**
     * エイリアスを除外したURLを取得
     *
     * @param string $url
     * @return mixed|string
     * @checked
     * @noTodo
     * @unitTest
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
     * エイリアスを反映したURLを生成
     * 同一URL設定のみ利用可
     *
     * @param ServerRequest $request リクエスト
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function makeUrl(ServerRequest $request)
    {
        $here = $request->getPath();
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
     * URLが存在するか確認
     *
     * @param ServerRequest $request
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function existsUrl(ServerRequest $request)
    {
        $url = $this->makeUrl($request);
        if (strpos($url, '?') !== false) {
            $url = explode('?', $url)[0];
        }
        /* @var Content $Content */
        $Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        if ($Contents->findByUrl($url, true, false, $this->same_main_url, $this->use_subdomain) ||
            $Contents->findByUrl($url, true, true, $this->same_main_url, $this->use_subdomain)) {
            return true;
        }
        return false;
    }

    /**
     * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
     *
     * @param ServerRequest $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     */
    public function shouldRedirects(ServerRequest $request)
    {
        if (!$this->status || !$this->existsUrl($request)) {
            return false;
        }
        if (!$this->status || !$this->autoRedirect) {
            return false;
        }
        $autoRedirectKey = "{$this->name}_auto_redirect";
        $session = $request->getSession();
        if ($request->getQuery($autoRedirectKey)
            && in_array($request->getQuery($autoRedirectKey), ['on', 'off'])) {
            $session->write($autoRedirectKey, $request->getQuery($autoRedirectKey));
        }
        if ($request->getQuery($this->name)) {
            switch($request->getQuery($this->name)) {
                case 'on':
                    return true;
                case 'off':
                    return false;
            }
        }
        return $session->read($autoRedirectKey) !== 'off';
    }

}
