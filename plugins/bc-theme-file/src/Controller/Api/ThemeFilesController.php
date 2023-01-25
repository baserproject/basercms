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
use BcThemeFile\Service\ThemeFilesServiceInterface;

/**
 * テーマファイルコントローラー
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
            $form = $service->create($this->getRequest()->getData());
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
     */
    public function edit(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI ファイル編集 #1770
    }

    /**
     * [API] テーマファイル ファイル削除
     *
     * @param ThemeFilesServiceInterface $service
     */
    public function delete(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI ファイル削除 #1772
    }

    /**
     * [API] テーマファイル ファイルコピー
     *
     * @param ThemeFilesServiceInterface $service
     */
    public function copy(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI ファイルコピー #1773
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
     */
    public function view(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI ファイルを表示 #1775
    }

    /**
     * [API] テーマファイル 画像を表示
     *
     * @param ThemeFilesServiceInterface $service
     */
    public function img(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI 画像を表示 #1776
    }

    /**
     * [API] テーマファイル 画像のサムネイルを表示
     *
     * @param ThemeFilesServiceInterface $service
     */
    public function img_thumb(ThemeFilesServiceInterface $service)
    {
        //todo テーマファイルAPI 画像のサムネイルを表示 #1777
    }
}
