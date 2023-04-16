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

namespace BcThemeFile\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BaserCore\Error\BcFormFailedException;
use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcBaserHelper;
use BcThemeFile\Service\ThemeFilesServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * テーマファイルコントローラー
 * @property BcBaserHelper $BcBaser
 */
class ThemeFilesController extends BcAdminApiController
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $form = $service->create($data);
            $entity = $service->get($form->getData('fullpath'));
            $message = __d('baser_core', 'ファイル「{0}」を作成しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
            $errors = $e->getEntity()->getErrors();
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $themeFileForm = $service->update($data);
            $entity = $service->get($themeFileForm->getData('fullpath'));
            $message = __d('baser_core', 'ファイル「{0}」を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
            $errors = $e->getEntity()->getErrors();
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $themeFile = $service->get($data['fullpath'])->toArray();
            if ($service->delete($data['fullpath'])) {
                $message = __d('baser_core', 'ファイル「{0}」を削除しました。', $data['path']);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $message = __d('baser_core', 'ファイル「{0}」の削除に失敗しました。', $data['path']);
            }
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'themeFile' => $themeFile ?? null,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $entity = $service->copy($data['fullpath']);
            if ($entity) {
                $message = __d('baser_core', 'ファイル「{0}」をコピーしました。', $data['path']);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $message = __d('baser_core', 'ファイル「{0}」のコピーに失敗しました。上位フォルダのアクセス権限を見直してください。', $data['path']);
            }
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'themeFile' => $entity ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'themeFile']);
    }

    /**
     * [API] テーマファイル 現在のテーマにファイルをコピー
     *
     * @param ThemeFilesServiceInterface $service
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function copy_to_theme(ThemeFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);

        try {
            $data = $this->getRequest()->getData();
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $data['assets'] = in_array($data['type'], ['css', 'js', 'img']);
            $targetPath = $service->copyToTheme($data);
            $currentTheme = BcUtil::getCurrentTheme();
            $message = __d('baser_core',
                'コアファイル {0} を テーマ {1} の次のパスとしてコピーしました。\n{2}。',
                basename($data['path']),
                $currentTheme,
                $targetPath
            );
            $this->BcMessage->setSuccess($message, true, false);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'entity' => $entity ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'entity']);
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $entity = $service->get($data['fullpath']);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。');
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $imgDetail = $service->getImg($data);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'img' => base64_encode($imgDetail['img']) ?? null,
            'message' => $message ?? null
        ]);
        $this->viewBuilder()->setOption('serialize', ['img', 'message']);
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $imgDetail = $service->getImgThumb($data, $data['width'], $data['height']);
        } catch (BcFormFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
            $errors = $e->getEntity()->getErrors();
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
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
            $data['fullpath'] = $service->getFullpath($data['theme'], $data['plugin'], $data['type'], $data['path']);
            $service->upload($data['fullpath'], $data);
            $message = __d('baser_core', 'アップロードに成功しました。');
            $this->BcMessage->setSuccess($message, true, false);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }
}
