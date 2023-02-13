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

namespace BcThemeFile\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BaserCore\Error\BcFormFailedException;
use BcThemeFile\Service\ThemeFoldersService;
use BcThemeFile\Service\ThemeFoldersServiceInterface;

/**
 * テーマフォルダコントローラー
 */
class ThemeFoldersController extends BcApiController
{

    /**
     * テーマフォルダのバッチ処理
     *
     * 指定したテーマフォルダに対して削除の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete' 以外の値であれば500エラーを発生させる
     *
     * @param ThemeFoldersService $service
     * @checked
     * @noTodo
     */
    public function batch(ThemeFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除',
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            if (is_dir($targets[0])) {
                $fullpath = $targets[0];
            } else {
                $fullpath = dirname($targets[0]);
            }
            $names = $service->getNamesByFullpath($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                __d('baser', 'フォルダー {0} 内の「{1}」を {2} しました。', $fullpath, implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * テーマフォルダAPI 一覧取得
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ThemeFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        try {
            $data = $this->getRequest()->getQueryParams();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $themeFiles = $service->getIndex($data);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getForm()->getErrors();
            $message = __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'themeFiles' => $themeFiles ?? null,
            'message' => $message ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['themeFiles', 'message', 'errors']);
    }

    /**
     * テーマフォルダAPI テーマフォルダ新規追加
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(ThemeFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $form = $service->create($data);
            $themeFolder = $service->get($form->getData('fullpath'));
            $message = __d('baser', 'フォルダ「{0}」を作成しました。', $themeFolder->name);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getForm()->getErrors();
            $message = __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'message' => $message,
            'themeFolder' => $themeFolder ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'themeFolder', 'errors']);
    }

    /**
     * テーマフォルダAPI テーマフォルダ編集
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ThemeFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $form = $service->update($data);
            $themeFolder = $service->get($form->getData('fullpath'));
            $message = __d('baser', 'フォルダ名を「{0}」に変更しました。', $themeFolder->name);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getForm()->getErrors();
            $message = __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'message' => $message,
            'themeFolder' => $themeFolder ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'themeFolder', 'errors']);
    }

    /**
     * テーマフォルダAPI テーマフォルダ削除
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ThemeFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $themeFolder = $service->get($data['fullpath']);
            if ($service->delete($data['fullpath'])) {
                $message = __d('baser', 'フォルダ「{0}」を削除しました。', $data['path']);
            } else {
                $message = __d('baser', 'フォルダ「{0}」の削除に失敗しました。', $data['path']);
            }
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getForm()->getErrors();
            $message = __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'themeFolder' => $themeFolder ?? null,
            'message' => $message,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['themeFolder', 'message', 'errors']);
    }

    /**
     * テーマフォルダAPI テーマフォルダコピー
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     */
    public function copy(ThemeFoldersServiceInterface $service)
    {
        //todo テーマフォルダAPI テーマフォルダコピー
    }

    /**
     * テーマフォルダAPI 現在のテーマにテーマフォルダをコピー
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     */
    public function copy_to_theme(ThemeFoldersServiceInterface $service)
    {
        //todo テーマフォルダAPI 現在のテーマにテーマフォルダをコピー
    }

    /**
     * テーマフォルダAPI フォルダを表示
     *
     * @param ThemeFoldersServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ThemeFoldersServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        try {
            $data = $this->getRequest()->getQueryParams();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $entity = $service->get($data['fullpath']);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'entity' => $entity ?? null,
            'message' => $message ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['entity', 'message', 'errors']);
    }

}
