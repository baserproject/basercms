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

namespace BaserCore\Middleware;

use BaserCore\Utility\BcAgent;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcRequestFilterMiddleware
 */
class BcRequestFilterMiddleware implements MiddlewareInterface
{

    /**
     * Process
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $request = $this->addDetectors($request);

        if ($this->isAsset($request)) {
            Configure::write('BcRequest.asset', true);
            return new Response();
        }

        $this->redirectIfIsDeviceFile($request, $handler);

        return $handler->handle($request);
    }

    /**
     * デバイス用ファイルへのアクセスの場合リダイレクト
     *
     * /m/files/... へのアクセスの場合、/files/... へ自動リダイレクト
     * CMSで作成するページ内のリンクは、モバイルでアクセスすると、自動的に、/m/ 付のリンクに書き換えられてしまう為、
     * files内のファイルへのリンクがリンク切れになってしまうので暫定対策。
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface|void
     * @checked
     * @unitTest
     */
    public function redirectIfIsDeviceFile(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler)
    {
        // TODO ucmitz ユニットテストでしか動作確認していない
        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($request->getPath());
        if ($site && $site->device) {
            $param = preg_replace('/^\/' . $site->alias . '\//', '', $request->getPath());
            if (preg_match('/^files/', $param)) {
                $response = new Response();
                $response = $response->withStatus(301);
                return $response->withLocation(BcUtil::topLevelUrl(false) . "{$request->getAttribute('base')}/{$param}");
            }
        }
    }

    /**
     * リクエスト検出器の設定を取得
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDetectorConfigs()
    {
        $configs = [];
        $configs['admin'] = [$this, 'isAdmin'];
        $configs['asset'] = [$this, 'isAsset'];
        $configs['install'] = [$this, 'isInstall'];
        $configs['maintenance'] = [$this, 'isMaintenance'];
        $configs['update'] = [$this, 'isUpdate'];
        $configs['page'] = [$this, 'isPage'];
        $configs['requestview'] = [$this, 'isRequestView'];

        $agents = BcAgent::findAll();
        foreach($agents as $agent) {
            $configs[$agent->name] = ['env' => 'HTTP_USER_AGENT', 'pattern' => $agent->getDetectorRegex()];
        }
        return $configs;
    }

    /**
     * リクエスト検出器を追加する
     *
     * @param ServerRequest $request リクエスト
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addDetectors(ServerRequest $request): ServerRequest
    {
        foreach($this->getDetectorConfigs() as $name => $callback) {
            $request->addDetector($name, $callback);
        }
        return $request;
    }

    /**
     * 管理画面のURLかどうかを判定
     *
     * @param ServerRequestInterface $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAdmin(ServerRequestInterface $request)
    {
        $regex = '/^' . preg_quote(Configure::read('BcApp.baserCorePrefix') . Configure::read('BcApp.adminPrefix'), '/') . '($|\/)/';
        return (bool)preg_match($regex, $request->getPath());
    }

    /**
     * アセットのURLかどうかを判定
     *
     * @param ServerRequestInterface $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAsset(ServerRequestInterface $request)
    {
        $dirs = ['css', 'js', 'img'];
        $exts = ['css', 'js', 'gif', 'jpg', 'jpeg', 'png', 'ico', 'svg', 'swf'];

        $dirRegex = implode('|', $dirs);
        $extRegex = implode('|', $exts);

        $assetRegex = '/^\/(' . $dirRegex . ')\/.+\.(' . $extRegex . ')$/';
        $themeAssetRegex = '/^\/theme\/[^\/]+?\/(' . $dirRegex . ')\/.+\.(' . $extRegex . ')$/';

        $uri = $request->getPath();
        return preg_match($assetRegex, $uri) || preg_match($themeAssetRegex, $uri);
    }

    /**
     * インストール用のURLかどうかを判定
     * [注]ルーターによるURLパース後のみ
     *
     * @param ServerRequestInterface $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isInstall(ServerRequestInterface $request)
    {
        return $request->getParam('controller') === 'Installations';
    }

    /**
     * メンテナンス用のURLかどうかを判定
     *
     * @param ServerRequestInterface $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isMaintenance(ServerRequestInterface $request)
    {
        $slug = '/maintenance';
        return in_array($request->getPath(), [$slug, "{$slug}/", "{$slug}/index"]);
    }

    /**
     * アップデート用のURLかどうかを判定
     *
     * @param ServerRequestInterface $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isUpdate(ServerRequestInterface $request)
    {
        $slug = '/' . Configure::read('BcApp.updateKey');
        return in_array($request->getPath(), [$slug, "{$slug}/", "{$slug}/index"]);
    }

    /**
     * 固定ページ表示用のURLかどうかを判定
     * [注]ルーターによるURLパース後のみ
     *
     * @param ServerRequestInterface $request リクエスト
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isPage(ServerRequestInterface $request)
    {
        return $request->getParam('controller') === 'Pages'
            && $request->getParam('action') === 'display';
    }

    /**
     * baserCMSの基本処理を必要とするかどうか
     *
     * @param ServerRequestInterface $request
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isRequestView(ServerRequestInterface $request)
    {
        return !($request->getQuery('requestview') && $request->getQuery('requestview') === "false");
    }

}
