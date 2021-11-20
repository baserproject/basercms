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

namespace BaserCore\Routing\Route;

use BaserCore\Service\BcFrontService;
use BaserCore\Service\SiteService;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcContentsRoute
 */
class BcContentsRoute extends Route
{

    /**
     * Parse
     * @param string $url
     * @param string $method
     * @return array|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function parse($url, $method): ?array
    {
        if (is_array($url)) {
            return null;
        }
        if (BcUtil::isAdminSystem($url)) {
            return null;
        }
        $request = Router::getRequest();
        if (!$request) {
            return null;
        }

        //管理システムにログインしているかつプレビューの場合は公開状態のステータスは無視する
        $publish = true;
        if ((!empty($request->getQuery['preview']) || !empty($request->getQuery['force'])) && BcUtil::loginUser()) {
            $publish = false;
            if (!empty($request->getQuery['host'])) {
                Configure::write('BcEnv.host', $request->getQuery['host']);
            } else {
                Configure::write('BcEnv.host', '');
            }
        }

        $sameUrl = false;
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->getSubByUrl($url, true);
        if ($site) {
            // 同一URL対応
            $sameUrl = true;
            $checkUrl = $site->makeUrl($request);
            @header('Vary: User-Agent');
        } else {
            $site = $sites->findByUrl($url);
            if (!is_null($site->name)) {
                if ($site->use_subdomain) {
                    $checkUrl = '/' . $site->alias . (($url)? $url : '/');
                } else {
                    $checkUrl = ($url)? $url : '/';
                }
            } else {
                if (!empty($request->getQuery['force']) && BcUtil::isAdminUser()) {
                    // =================================================================================================
                    // 2016/11/10 ryuring
                    // 別ドメインの際に、固定ページのプレビューで、正しくサイト情報を取得できない。
                    // そのため、文字列でリクエストアクションを送信し、URLでホストを判定する。
                    // =================================================================================================
                    $tmpSite = $sites->findByUrl($url);
                    if (!is_null($tmpSite)) {
                        $site = $tmpSite;
                    }
                }
                $checkUrl = (($url)? $url : '/');
            }
        }

        $contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $content = $contents->findByUrl($checkUrl, $publish, false, $sameUrl, $site->use_subdomain);
        if (!$content) {
            $content = $contents->findByUrl($checkUrl, $publish, true, $sameUrl, $site->use_subdomain);
        }

        if (!$content) {
            return null;
        }

        // 管理画面にログインしていないとき、リダイレクトする設定ならば処理をする
        $redirect = Configure::read('BcAuthPrefix.admin.previewRedirect');
        if ($redirect) {
            // データが存在してもプレビューで管理システムにログインしていない場合はログイン画面に遷移
            if ((!empty($request->geQuery['preview']) || !empty($request->geQuery['force'])) && !BcUtil::loginUser()) {
                $_SESSION['Auth']['redirect'] = $_SERVER['REQUEST_URI'];
                header('Location: ' . BcUtil::topLevelUrl(false) . baseUrl() . Configure::read('BcAuthPrefix.admin.alias') . '/users/login');
                exit();
            }
        }

        if ($content->alias_id && !$contents->isPublishById($content->alias_id)) {
            return null;
        }
        $url = $site->getPureUrl($url);
        $params = $this->getParams($url, $content->url, $content->plugin, $content->type, $content->entity_id, $site->alias);
        $params['Content'] = isset($content)? $content : null;
        $params['Site'] = isset($content->site)? $content->site : null;
        if ($params) {
            return $params;
        }
        return null;
    }

    /**
     * コンテンツに関連するパラメーター情報を取得する
     *
     * @param $requestUrl
     * @param $entryUrl
     * @param $plugin
     * @param $type
     * @param $entityId
     * @return mixed false|array
     */
    public function getParams($requestUrl, $entryUrl, $plugin, $type, $entityId, $alias)
    {
        $viewParams = Configure::read('BcContents.items.' . $plugin . '.' . $type . '.routes.view');
        if (!$viewParams) {
            $viewParams = Configure::read('BcContents.items.BaserCore.Default.routes.view');
            $params = [
                'controller' => $viewParams['controller'],
                'action' => $viewParams['action'],
                'pass' => [$plugin, $type]
            ];
        } else {
            $pass = [];
            $named = [];
            $action = $viewParams['action'];
            // 別ドメインの場合は１階層目を除外
            $pureEntryUrl = $entryUrl;
            if ($alias) {
                $pureEntryUrl = preg_replace('/^\/' . preg_quote($alias, '/') . '\//', '/', $pureEntryUrl);
            }
            if ($type == 'Page' || $type == 'ContentLink') {
                $url = preg_replace('/^\//', '', $entryUrl);
                $pass = explode('/', $url);
            } elseif ($requestUrl != $pureEntryUrl) {
                // コントローラーとなり得る部分までの文字列を除外してアクションを取得
                $url = preg_replace('/^' . preg_quote($pureEntryUrl, '/') . '/', '', $requestUrl);
                $url = preg_replace('/^\//', '', $url);
                $urlAry = explode('/', $url);
                $action = $urlAry[0];
                // アクション部分を除外
                array_shift($urlAry);
                // パラメーターを取得（pass / named）
                if ($urlAry) {
                    foreach($urlAry as $param) {
                        if (strpos($param, ':') !== false) {
                            [$key, $value] = explode(':', $param);
                            $named[$key] = $value;
                        } else {
                            $pass[] = $param;
                        }
                    }
                }
            }
            $controllerClass = Inflector::camelize($viewParams['controller']) . 'Controller';
            $controllerClass = ($plugin)? $plugin . '\\Controller\\' . $controllerClass : 'App\\Controller\\' . $controllerClass;
            $methods = get_class_methods($controllerClass);
            if (!in_array($action, $methods)) {
                return false;
            }
            $params = [
                'plugin' => $plugin,
                'controller' => Inflector::camelize($viewParams['controller']),
                'action' => $action,
                'pass' => $pass,
                'named' => $named,
                'entityId' => $entityId
            ];
        }
        $params['_matchedRoute'] = $this->template;
        return $params;
    }

    /**
     * Reverse route
     *
     * @param array $url Array of parameters to convert to a string.
     * @return mixed either false or a string URL.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function match($url, $context = []): string
    {

        // フロント以外のURLの場合にマッチしない
        if (!empty($url['admin'])) {
            return false;
        }

        // プラグイン確定
        if (empty($url['plugin'])) {
            $plugin = 'BaserCore';
        } else {
            $plugin = Inflector::camelize($url['plugin']);
        }

        // アクション確定
        if (!empty($url['action'])) {
            $action = $url['action'];
        } else {
            $action = 'index';
        }

        $params = $url;
        unset($params['plugin']);
        unset($params['controller']);
        unset($params['action']);
        unset($params['entityId']);
        unset($params['_ext']);

        // コンテンツタイプ確定、できなければスルー
        $type = $this->_getContentTypeByParams($url);
        if ($type) {
            unset($params['action']);
        } else {
            $type = $this->_getContentTypeByParams($url, false);
        }
        if (!$type) {
            return false;
        }

        // エンティティID確定
        $entityId = $contentId = null;
        if (isset($url['entityId'])) {
            $entityId = $url['entityId'];
        } else {
            $request = Router::getRequest(true);
            if (!empty($request->getParam('entityId'))) {
                $entityId = $request->getParam('entityId');
            }
            if (!empty($request->getParam('Content.alias_id'))) {
                $contentId = $request->getParam('Content.id');
            }
        }

        // コンテンツ確定、できなければスルー
        if ($type == 'Page') {
            $pass = [];
            foreach($params as $key => $param) {
                if (!is_string($key)) {
                    $pass[] = $param;
                    unset($params[$key]);
                }
            }
            $strUrl = '/' . implode('/', $pass);
        } else {
            $contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
            if ($contentId) {
                $conditions = ['Contents.id' => $contentId];
            } else {
                $conditions = [
                    'Contents.plugin' => $plugin,
                    'Contents.type' => $type,
                    'Contents.entity_id' => $entityId
                ];
            }
            $strUrl = $contents->find()->where($conditions)->first()->url;
        }

        if (!$strUrl) {
            return false;
        }

        // URL生成
        $sites = new SiteService();
        $site = $sites->findByUrl($strUrl);
        if ($site && $site->use_subdomain) {
            $strUrl = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/', $strUrl);
        }
        $pass = [];
        $named = [];
        $setting = Configure::read('BcContents.items.' . $plugin . '.' . $type);
        if (!$params) {
            if (empty($setting['omitViewAction']) && $setting['routes']['view']['action'] != $action) {
                $strUrl .= '/' . $action;
            }
        } else {
            if (empty($setting['omitViewAction'])) {
                $strUrl .= '/' . $action;
            }
            foreach($params as $key => $param) {
                if (!is_numeric($key)) {
                    if ($key == 'page' && !$param) {
                        $param = 1;
                    }
                    if (!is_array($param)) {
                        $named[] = $key . ':' . $param;
                    }
                } else {
                    $pass[] = $param;
                }
            }
        }
        if ($pass) {
            $strUrl .= '/' . implode('/', $pass);
        }
        if ($named) {
            $strUrl .= '/' . implode('/', $named);
        }
        if ($type === 'ContentFolder') {
            $strUrl .= '/';
        }
        return $strUrl;
    }

    /**
     * パラメーターよりコンテンツタイプを取得する
     *
     * @param array $params パラメーター
     * @param bool $useAction アクションを判定に入れるかどうか
     * @return bool|string
     */
    protected function _getContentTypeByParams($params, $useAction = true)
    {
        $plugin = Inflector::camelize($params['plugin']);
        $items = Configure::read('BcContents.items.' . $plugin);
        if (!$items) {
            return false;
        }
        foreach($items as $key => $item) {
            if (empty($item['routes']['view'])) {
                continue;
            }
            $viewParams = $item['routes']['view'];
            if ($useAction) {
                if ($params['controller'] == $viewParams['controller'] && $params['action'] == $viewParams['action']) {
                    return $key;
                }
            } else {
                if ($params['controller'] == $viewParams['controller']) {
                    return $key;
                }
            }
        }
        return false;
    }

}
