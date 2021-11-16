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
            if (!empty($params) && $params['prefix'] === 'Admin') {
                $controller->viewBuilder()->setTheme('BcAdminThird');
            }
            // TODO: フロントのデフォルトエラー画面とそれを上書きできる仕組み
        }
        return $controller;
    }
}
