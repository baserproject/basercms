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

use BaserCore\Error\BcException;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;

/**
 * Class ThemesController
 *
 * https://localhost/baser/api/baser-core/themes/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class ThemesController extends BcApiController
{
    /**
     * [API] テーマ一覧を取得する
     * @param ThemesServiceInterface $themes
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ThemesServiceInterface $themes)
    {
        $this->set([
            'themes' => $themes->getIndex()
        ]);
        $this->viewBuilder()->setOption('serialize', ['themes']);
    }

    /**
     * [API] 新しいテーマをアップロードする
     * @param ThemesServiceInterface $service
     * @noTodo
     * @checked
     * @unitTest
     */
    public function add(ThemesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $errors = [];
        try {
            $theme = $service->add($this->getRequest()->getUploadedFiles());
            $message = __d('baser', 'テーマファイル「' . $theme . '」を追加しました。');
        } catch (BcException $e) {
            $errors = $e->getMessage();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'ファイルのアップロードに失敗しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'theme' => $theme,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'theme', 'errors']);
    }
    /**
     * [API] テーマを削除する
     *
     * @param ThemesServiceInterface $service
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ThemesServiceInterface $service, string $theme)
    {
        $this->request->allowMethod(['post']);

        $error = null;
        try {
            $theme = $service->get($theme);
            $service->delete($theme->name);
            $message = __d('baser', 'テーマ「{0}」を削除しました。', $theme->name);
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $error = $e->getMessage();
            $message = __d('baser', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage());
        }

        $this->set([
            'theme' => $theme,
            'message' => $message,
            'error' => $error
        ]);

        $this->viewBuilder()->setOption('serialize', ['theme', 'message', 'error']);
    }

    /**
     * [API] テーマをコピーする
     *
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(ThemesServiceInterface $service, $theme)
    {
        $this->request->allowMethod(['post']);

        $error = null;
        try {
            $rs = $service->copy($theme);
            if ($rs) {
                $message = __d('baser', 'テーマ「{0}」をコピーしました。', $theme);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'テーマ「{0}」のコピーに失敗しました。', $theme);
            }
            $theme = $service->get($theme);

        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $error = $e->getMessage();
            $message = __d('baser', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage());
        }
        $this->set([
            'theme' => $theme,
            'message' => $message,
            'error' => $error
        ]);

        $this->viewBuilder()->setOption('serialize', ['theme', 'message', 'error']);
    }

    /**
     * [API] テーマの初期データを読み込むAPIを実装
     * @param ThemesServiceInterface $themesService
     * @param SitesServiceInterface $sitesService
     * @param int $siteId
     * @noTodo
     */
    public function load_default_data(ThemesServiceInterface $themesService, SitesServiceInterface $sitesService, int $siteId)
    {
        $this->request->allowMethod(['post']);

        $errors = null;

        if (empty($this->getRequest()->getData('default_data_pattern'))) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '不正な操作です。');
        } else {
            try {
                $result = $themesService->loadDefaultDataPattern($sitesService->get($siteId), $this->getRequest()->getData('default_data_pattern'));
                if (!$result) {
                    $this->setResponse($this->response->withStatus(400));
                    $message = __d('baser', '初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。');
                } else {
                    $message = __d('baser', '初期データの読み込みが完了しました。');
                }
            } catch (BcException $e) {
                $errors = $e->getMessage();
                $message = __d('baser', '初期データの読み込みに失敗しました。');
                $this->setResponse($this->response->withStatus(400));
            }
        }

        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }
    /**
     * [API] テーマを適用するAPI
     * @param ThemesServiceInterface $themesService
     * @param SitesServiceInterface $sitesService
     * @param int $siteId
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(ThemesServiceInterface $themesService, SitesServiceInterface $sitesService, int $siteId, string $theme)
    {
        $this->request->allowMethod(['post']);

        $errors = null;

        try {
            $info = $themesService->apply($sitesService->get($siteId), $theme);
            $theme = $themesService->get($theme);
            $message = [__d('baser', 'テーマ「{0}」を適用しました。', $theme->name)];
            if ($info) $message = array_merge($message, [''], $info);
            $message = implode("\n", $message);
        } catch (BcException $e) {
            $errors = $e->getMessage();
            $message = __d('baser', 'テーマの適用に失敗しました。', $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'theme' => $theme,
            'siteId' => $siteId,
            'errors' => $errors
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'theme', 'siteId', 'errors']);
    }
}
