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

namespace BcFavorite\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BaserCore\Utility\BcUtil;
use BcFavorite\Service\FavoritesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class FavoritesController
 */
class FavoritesController extends BcApiController
{

    /**
     * お気に入り情報取得
     * @param FavoritesServiceInterface $service
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(FavoritesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['get']);
        $favorite = $message = null;
        try {
            $favorite = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'favorite' => $favorite,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorite', 'message']);
    }

    /**
     * お気に入り情報一覧取得
     * @param FavoritesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(FavoritesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'favorites' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorites']);
    }

    /**
     * お気に入り情報登録
     * @param FavoritesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function add(FavoritesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $favorite = $errors = null;
        try {
            $favorite = $service->create($this->request->getData());
            $message = __d('baser_core', 'お気に入り「{0}」を追加しました。', $favorite->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'favorite' => $favorite,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'favorite', 'errors']);
    }

    /**
     * お気に入り情報編集
     * @param FavoritesServiceInterface $service
     * @param $id
     * @checked
     * @noTodo
     */
    public function edit(FavoritesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $favorite = $errors = null;
        try {
            $favorite = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'お気に入り「{0}」を更新しました。', $favorite->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'favorite' => $favorite,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorite', 'message', 'errors']);
    }

    /**
     * お気に入り情報削除
     * @param FavoritesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     */
    public function delete(FavoritesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $favorite = $errors = $message = null;
        try {
            $favorite = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser_core', 'お気に入り: {0} を削除しました。', $favorite->name);
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'favorite' => $favorite,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorite', 'message', 'errors']);
    }

    /**
     * [ADMIN] 並び替えを更新する
     *
     * @return void
     */
    public function change_sort(FavoritesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $result = $message = null;
        try {
            $user = BcUtil::loginUser();
            $result = $service->changeSort(
                $this->request->getData('id'),
                $this->request->getData('offset'),
                ['Favorites.user_id' => $user->id]
            );
            if ($result) BcUtil::clearAllCache();
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'result' => $result,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['result', 'message']);
    }

    /**
     * よく使う項目の表示状態を保存する
     *
     * @param mixed $open 1 Or ''
     */
    public function save_favorite_box($open = '')
    {
        $message = null;
        try {
            if ($open === '1' || $open === '') {
                $this->request->getSession()->write('Baser.favorite_box_opened', $open);
            } else {
                $this->setResponse($this->response->withStatus(400));
            }
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * よく使う項目の表示状態を取得する
     *
     * @return  1 Or ''
     */
    public function get_favorite_box_opened()
    {
        $result = $message = null;
        try {
            $result = $this->request->getSession()->read('Baser.favorite_box_opened');
            $this->set([
                'result' => $result
            ]);
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'result' => $result,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['result', 'message']);
    }
}
