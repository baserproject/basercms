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

namespace BaserCore\Error;

use BaserCore\Controller\BcErrorController;
use Cake\Controller\Controller;
use Cake\Error\ExceptionRenderer;
use Cake\Routing\Router;

class BcExceptionRenderer extends ExceptionRenderer
{
    protected function _getController(): Controller
    {
        $controller = parent::_getController();
        if (!$controller->viewBuilder()->getTheme()) {
            $params = Router::getRequest()->getAttribute('params');
            if (isset($params['prefix'])) {
                if ($params['prefix'] === 'Admin') {
                    $controller->viewBuilder()->setTheme('BcAdminThird');
                }
            } else {
                return new BcErrorController();
            }
        }
        return $controller;
    }
}
