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

namespace BaserCore\Service\Front;

use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PagesFrontServiceInterface
 */
interface PagesFrontServiceInterface
{

    /**
     * 固定ページ用のデータを取得する
     * @param EntityInterface $page
     * @param ServerRequest $request
     * @return array
     */
    public function getViewVarsForView(EntityInterface $page, ServerRequest $request): array;

    /**
     * プレビューのためのセットアップを行う
     * @param Controller $controller
     */
    public function setupPreviewForView(Controller $controller): void;

}
