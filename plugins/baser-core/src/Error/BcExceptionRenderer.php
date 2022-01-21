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
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * BcExceptionRenderer
 */
class BcExceptionRenderer extends ExceptionRenderer
{

    /**
     * _getController
     * @return Controller
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function _getController(): Controller
    {
        $controller = parent::_getController();
        $request = $controller->getRequest();
        if (!$request->is('json') && !$controller->viewBuilder()->getTheme()) {
            return new BcErrorController();
        }
        return $controller;
    }
}
