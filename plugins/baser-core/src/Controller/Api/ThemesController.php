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
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
}
