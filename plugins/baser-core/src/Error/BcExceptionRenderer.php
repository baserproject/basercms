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

namespace BaserCore\Error;

use BaserCore\Controller\BcErrorController;
use Cake\Controller\Controller;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use Cake\Error\Renderer\WebExceptionRenderer;

/**
 * BcExceptionRenderer
 */
class BcExceptionRenderer extends WebExceptionRenderer
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
            return new BcErrorController($request);
        }
        return $controller;
    }

}
