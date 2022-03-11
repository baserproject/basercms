<?php

namespace BaserCore\Controller\Admin;

use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

class PreviewController extends BcAdminAppController
{

    use BcContainerTrait;

    public function view()
    {
        $query = $this->getRequest()->getQueryParams();
        $url = $query['url'];
        $request = $this->createRequest($url);
        $serviceName = $request->getParam('plugin') . '\\Service\\' . $request->getParam('controller') . Inflector::camelize($request->getParam('action')) . 'ServiceInterface';
        $service = $this->getService($serviceName);
        $this->set($service->getPreviewData($this->getRequest()));
        $this->viewBuilder()->setTemplate($request->getParam('action'));
        $this->viewBuilder()->setTemplatePath($request->getParam('controller'));
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
