<?php

namespace BaserCore\Controller\Admin;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Exception\ForbiddenException;

class PreviewController extends BcAdminAppController
{

    use BcContainerTrait;

    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    public function view(...$path)
    {
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        if ($path && $this->getRequest()->getData()) {
            $request = $this->createRequest("/$path[0]");
            $serviceName = $request->getParam('plugin') . '\\Service\\' . $request->getParam('controller') . Inflector::camelize($request->getParam('action')) . 'ServiceInterface';
            $service = $this->getService($serviceName);
            $this->setRequest($request);
            $a = $service->getPreviewData($this->getRequest());
            $this->set($service->getPreviewData($this->getRequest()));
            $this->set('pageContent', $this->getRequest()->getParam('Page.contents'));
            $this->viewBuilder()->setTemplate($request->getParam('action'));
            $this->viewBuilder()->setTemplatePath($request->getParam('controller'));
            // $template = !empty($this->getRequest()->getParam('Page.page_template')) ? $this->getRequest()->getParam('Page.page_template') : "default";
            return $this->render();
        } else {
            $this->BcMessage->setError('プレビューが適切ではありません。');
            return $this->redirect($this->referer());
        }
    }

    public function createRequest($url): ServerRequest
    {
        $config = [];
        $method = 'GET';
        if(preg_match('/^http/', $url)) {
            $parseUrl = parse_url($url);
            Configure::write('BcEnv.host', $parseUrl['host']);
            $defaultConfig = [
                'uri' => ServerRequestFactory::createUri([
                    'HTTP_HOST' => $parseUrl['host'],
                    'REQUEST_URI' => $url,
                    'REQUEST_METHOD' => $method,
                    'HTTPS' => (preg_match('/^https/', $url))? 'on' : ''
            ])];
        } else {
            $defaultConfig = [
                'url' => $url,
                'environment' => [
                    'REQUEST_METHOD' => $method
            ]];
        }
        $defaultConfig = array_merge($defaultConfig, $config);
        $request = new ServerRequest($defaultConfig);
        // TODO ucmitz: webrootの解析ロジックを反映させる
        $request = $request->withAttribute('webroot', '/');
        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (\Exception $e) {
            return $request;
        }
        $request = $request->withAttribute('params', $params);
        return $request;
    }

}
