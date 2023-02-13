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
use BaserCore\View\Helper\BcBaserHelper;
use BcThemeFile\Service\ThemeFilesServiceInterface;

/**
 * テーマファイルコントローラー
 * @property BcBaserHelper $BcBaser
 */
class ThemeFilesController extends BcApiController
{

    /**
     * [API] テーマファイル ファイル新規追加
     *
     * @param ThemeFilesServiceInterface $service
     */
    public function add(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $form = $service->create($data);
            $entity = $service->get($form->getData('fullpath'));
            $message = __d('baser', 'ファイル「{0}」を作成しました。', $entity->name);
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
            'entity' => $entity ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'entity', 'errors']);
    }

    /**
     * [API] テーマファイル ファイル編集
     *
     * @param ThemeFilesServiceInterface $service
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function edit(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $themeFileForm = $service->update($data);
            $entity = $service->get($themeFileForm->getData('fullpath'));
            $message = __d('baser', 'ファイル「{0}」を更新しました。', $entity->name);
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
            'entity' => $entity ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'entity', 'errors']);
    }

    /**
     * [API] テーマファイル ファイル削除
     *
     * @param ThemeFilesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $themeFile = $service->get($data['fullpath']);
            if ($service->delete($data['fullpath'])) {
                $message = __d('baser', 'ファイル「{0}」を削除しました。', $data['path']);
            } else {
                $message = __d('baser', 'ファイル「{0}」の削除に失敗しました。', $data['path']);
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
            'themeFile' => $themeFile ?? null,
            'message' => $message,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['themeFile', 'message', 'errors']);
    }

    /**
     * [API] テーマファイル ファイルコピー
     *
     * @param ThemeFilesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            if ($service->copy($data['fullpath'])) {
                $message = __d('baser', 'ファイル「{0}」をコピーしました。', $data['path']);
            } else {
                $message = __d('baser', 'ファイル「{0}」のコピーに失敗しました。上位フォルダのアクセス権限を見直してください。', $data['path']);
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
            'message' => $message,
            'entity' => $entity ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'entity', 'errors']);
    }

    /**
     * [API] テーマファイル 現在のテーマにファイルをコピー
     *
     * @param ThemeFilesServiceInterface $service
     */
    public function copy_to_theme(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI 現在のテーマにファイルをコピー #1774
    }

    /**
     * [API] テーマファイル ファイルを表示
     *
     * @param ThemeFilesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ThemeFilesServiceInterface $service)
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

    /**
     * [API] テーマファイル 画像を表示
     *
     * @param ThemeFilesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function img(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        try {
            $data = $this->getRequest()->getQueryParams();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $imgDetail = $service->getImg($data);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getForm()->getErrors();
            $message = __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'img' => base64_encode($imgDetail['img']) ?? null,
            'message' => $message ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['img', 'message', 'errors']);
    }

    /**
     * [API] テーマファイル 画像のサムネイルを表示
     *
     * @param ThemeFilesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function img_thumb(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        try {
            $data = $this->getRequest()->getQueryParams();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $imgDetail = $service->getImgThumb($data, $data['width'], $data['height']);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getForm()->getErrors();
            $message = __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'imgThumb' => base64_encode($imgDetail['imgThumb']),
            'message' => $message ?? null,
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['imgThumb', 'message', 'errors']);
    }

    /**
     * テーマファイルアAPI テーマファイルアップロード
     *
     * @param ThemeFilesServiceInterface $service
     * @return void
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function upload(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['type'], $data['path']);
            $service->upload($data['fullpath'], $data);
            $message = __d('baser', 'アップロードに成功しました。');
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
            'errors' => $errors ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }
}
