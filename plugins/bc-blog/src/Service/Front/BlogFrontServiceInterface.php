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

namespace BcBlog\Service\Front;

use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogFrontServiceInterface
 */
interface BlogFrontServiceInterface
{

    /**
     * プレビュー用の view 変数を取得する
     * 
     * @param ServerRequest $request
     * @return array[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(ServerRequest $request): array;

    /**
     * プレビュー用のセットアップをする
     * 
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForIndex(Controller $controller): void;

}
