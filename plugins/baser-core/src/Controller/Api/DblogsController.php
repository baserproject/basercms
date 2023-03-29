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
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class DblogsController
 */
class DblogsController extends BcApiController
{

    /**
     * [API] DBログ一覧
     * @param DblogsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(DblogsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = array_merge([
            'contain' => null
        ], $this->getRequest()->getQueryParams());

        $this->set([
            'Dblogs' => $this->paginate($service->getIndex($queryParams))
        ]);

        $this->viewBuilder()->setOption('serialize', ['Dblogs']);
    }

    /**
     * [API] ログ新規追加
     * @param DblogsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(DblogsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        $dblog = $errors = null;
        try {
            $dblog = $service->create($this->request->getData());
            $message = __d('baser_core', 'ログを追加しました。');
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'dblog' => $dblog,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'dblog', 'errors']);
    }

    /**
     * [API] 最近の動きを削除
     * @param DblogsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete_all(DblogsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        if ($service->deleteAll()) {
            $message = __d('baser_core', '最近の動きのログを削除しました。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '最近の動きのログ削除に失敗しました。');
        }
        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
