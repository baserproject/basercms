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

namespace BaserCore\Controller\Admin;

use BaserCore\Utility\BcUtil;
use Cake\Http\Response;
use Cake\Routing\Router;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;
use Cake\Utility\Inflector;
use ReflectionClass;

class PreviewController extends BcAdminAppController
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * initialize
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Security->setConfig('unlockedActions', ['view']);
    }

    /**
     * view
     *
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view()
    {
        $request = $this->_createPreviewRequest($this->getRequest());
        try {
            Router::setRequest($request);
            $this->setRequest($request);
            $action = $request->getParam('action');
            $controller = $request->getParam('controller');
            $this->setName($controller);
            $this->viewBuilder()->setLayout('default');
            $this->viewBuilder()->setTemplate($action);
            $this->viewBuilder()->setTemplatePath($controller);
            $this->loadComponent('BaserCore.BcFrontContents');
            $this->BcFrontContents->setupFront();
            $this->setupFrontView();

            $serviceName = $request->getParam('plugin') . '\\Service\\Front\\' . $request->getParam('controller') . 'FrontServiceInterface';
            if($this->hasService($serviceName)) {
                $service = $this->getService($serviceName);
                $setupPreviewAction = 'setupPreviewFor' . Inflector::camelize($action);
                if (method_exists($service, $setupPreviewAction)) {
                    $service->{$setupPreviewAction}($this);
                }
            }
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            $this->BcMessage->setError(__d('baser_core', $e->getMessage()));
            return $this->redirect($this->referer());
        }
    }

    /**
     * プレビュー用のリクエストを作成する
     *
     * @param ServerRequest $request
     * @return ServerRequest
     * @checked
     * @noTodo
     * @unitTest
     */
    private function _createPreviewRequest($request)
    {
        $query = $request->getQueryParams();
        $url = $this->encodePath($query['url']);
        unset($query['url']);
        $params = [];
        foreach($query as $key => $value) {
            $params[] = $key . '=' . $value;
        }
        $url .= '?' . implode('&', $params);
        $request = BcUtil::createRequest(
            $url,
            $this->getRequest()->getData(),
            $this->getRequest()->getMethod()
        );
        //========================================================================
        // 2022/12/02 by ryuring
        // メールフォームのフォームを生成する際、$this->>formProtector が存在しないとエラーとなる。
        // formProtector をセットするには、FormHelper::create() 内にて、生成する必要があるが、
        // 生成条件として $request の attribute に formTokenData がセットされていないといけない。
        //
        // $request の attribute に formTokenData をセットするためには、
        // FormProtectionComponent を有効にするか、SecurityComponent で生成する必要がある。
        //
        // FormProtectionComponent では、_Tokenを送っても「_Token was not found in request data.」
        // となり、理由がわからず断念。SecurityComponent を利用する。
        //
        // SecurityComponent は、SecurityComponent::_validatePost() で引っかかってしまうため、
        // initialize でアンロックしている。
        // $this->Security->setConfig('unlockedActions', ['view']);
        //
        // そのため、自動で formTokenData が生成されないため、明示的にここで生成する。
        //========================================================================
        $request = $this->Security->generateToken($request);

        //========================================================================
        // 2022/12/02 by ryuring
        // 上記に続き、メールフォームの FormHelper::create() 内にて、formProtector を生成するには、
        // セッションが「正常に」スタートしている事が前提となる。
        //
        // リクエストの早い段階にてセッションはスタートしているが、$request を模倣する前提のため
        // PHPでは、セッションはスタート済で、 $request 内の セッションオブジェクトは未スタートという
        // 矛盾が発生してしまっているので、強制的にスタート済に設定している。
        //
        // BcUtil::createRequest() でやるべきかもしれないが影響範囲を考えここで記述
        // 上記に移行が問題なければ移行する
        //========================================================================
        $session = $request->getSession();
        $startedProperty = (new ReflectionClass($session))->getProperty('_started');
        $startedProperty->setAccessible(true);
        $startedProperty->setValue($session, true);
        $sessionProperty = (new ReflectionClass($request))->getProperty('session');
        $sessionProperty->setAccessible(true);
        $sessionProperty->setValue($request, $session);
        return $request;
    }

    /**
     * URLのパス部分を urlencode する
     *
     * @param string $url
     * @return string
     */
    public function encodePath(string $url) {
        $parseUrl = parse_url($url);
        $encoded = $parseUrl['scheme'] . '://' . $parseUrl['host'] . '/';
        $lastSlash = preg_match('/\/$/', $parseUrl['path']);
        $path = preg_replace('/^\//', '', $parseUrl['path']);
        $path = preg_replace('/\/$/', '', $path);
        $pathArray = explode('/', $path);
        foreach($pathArray as $key => $path) {
            $pathArray[$key] = urlencode($path);
        }
        $encoded .= implode('/', $pathArray) . (($lastSlash)? '/' : '');
        if(!empty($parseUrl['query'])) {
            $encoded .= '?' . $parseUrl['query'];
        }
        return $encoded;
    }

}
