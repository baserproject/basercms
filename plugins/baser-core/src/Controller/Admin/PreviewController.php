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
        $this->loadComponent('BaserCore.BcFrontContents');
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
            $this->viewBuilder()->setLayout('default');
            $this->viewBuilder()->setTemplate($action);
            $this->viewBuilder()->setTemplatePath($request->getParam('controller'));
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
            $this->BcMessage->setError(__d('baser', $e->getMessage()));
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
        $url = $query['url'];
        unset($query['url']);
        $params = [];
        foreach($query as $key => $value) {
            $params[] = $key . '=' . $value;
        }
        $url .= '?' . implode('&', $params);
        return BcUtil::createRequest(
            $url,
            $this->getRequest()->getData(),
            $this->getRequest()->getMethod()
        );
    }

}
