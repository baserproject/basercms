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

namespace BaserCore\Controller\Api;

use BaserCore\Service\ContentsServiceInterface;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ContentsController
 * @package BaserCore\Controller\Api
 */
class ContentsController extends BcApiController
{

    /**
     * index
     *
     * @param  ContentsServiceInterface $contents
     * @return void
     */
    public function index(ContentsServiceInterface $contents)
    {
        $this->set([
            'contents' => $this->paginate($contents->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }
}
