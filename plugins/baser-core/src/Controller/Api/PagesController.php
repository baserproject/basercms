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

use BaserCore\Service\PageServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class PagesController
 *
 * https://localhost/baser/api/baser-core/user_groups/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class PagesController extends BcApiController
{
    /**
     * 固定ページ一覧取得
     * @param PageServiceInterface $Pages
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(PageServiceInterface $Pages)
    {
        $this->request->allowMethod('get');
        $this->set([
            'pages' => $this->paginate($Pages->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['pages']);
    }

    /**
     * 固定ページ取得
     * @param PageServiceInterface $Pages
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(PageServiceInterface $Pages, $id)
    {
        $this->request->allowMethod('get');
        $this->set([
            'pages' => $Pages->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['pages']);
    }

    /**
     * 固定ページ登録
     * @param PageServiceInterface $Pages
     * @checked
     * @unitTest
     * @noTodo
     */
    public function add(PageServiceInterface $Pages)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $pages = $Pages->create($this->request->getData());
            $message = __d('baser', '固定ページ「{0}」を追加しました。', $pages->content->name);
            $this->set("page", $pages);
            $this->set('content', $pages->content);
        } catch (\Exception $e) {
            $pages = $e->getEntity();
            $a = $pages->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。\n");
            $this->set(['errors' => $pages->getErrors()]);
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'content', 'errors']);
    }

    /**
     * 固定ページ削除
     * @param PageServiceInterface $Pages
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(PageServiceInterface $Pages, $id)
    {
        $this->request->allowMethod(['delete']);
        $pages = $Pages->get($id);
        try {
            if ($Pages->delete($id)) {
                $message = __d('baser', '固定ページ: {0} を削除しました。', $pages->name);
            }
        } catch (Exception $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'pages' => $pages
        ]);
        $this->viewBuilder()->setOption('serialize', ['pages', 'message']);
    }

    /**
     * 固定ページー情報編集
     * @param PageServiceInterface $contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(PageServiceInterface $pages, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $page = $pages->update($pages->get($id), $this->request->getData());
            $message = __d('baser', 'フォルダー「{0}」を更新しました。', $page->name);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'page' => $page,
            'errors' => $page->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['page', 'message', 'errors']);
    }
}
