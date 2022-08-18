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

namespace BaserCore\Controller\Api;

use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class DblogsController
 * @package BaserCore\Controller\Api
 */
class DblogsController extends BcApiController
{

    /**
     * [API] DBログ一覧
     * @param DblogsServiceInterface $DblogsService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(DblogsServiceInterface $DblogsService)
    {
        $this->request->allowMethod(['get']);

        $this->set([
            'Dblogs' => $this->paginate($DblogsService->getIndex($this->request->getQueryParams()))
        ]);

        $this->viewBuilder()->setOption('serialize', ['Dblogs']);
    }

    /**
     * [API] 最近の動きを削除
     * @param DblogsServiceInterface $DblogsService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete_all(DblogsServiceInterface $DblogsService)
    {
        $this->request->allowMethod(['post', 'put']);

        if ($DblogsService->deleteAll()) {
            $message = __d('baser', '最近の動きのログを削除しました。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '最近の動きのログ削除に失敗しました。');
        }
        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
