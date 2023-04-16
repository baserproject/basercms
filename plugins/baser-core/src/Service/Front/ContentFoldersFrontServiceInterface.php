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
 * ContentFoldersFrontServiceInterface
 */
interface ContentFoldersFrontServiceInterface
{

    /**
     * フロント表示用の view 変数を取得する
     * @param EntityInterface $contentFolder
     * @param ServerRequest $request
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getViewVarsForView(EntityInterface $contentFolder, ServerRequest $request): array;

    /**
     * フロント用のテンプレートを取得する
     * @param $contentFolder
     * @return string
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getTemplateForView($contentFolder): string;

    /**
     * プレビュー用のセットアップをする
     * @param Controller $controller
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupPreviewForView(Controller $controller): void;

}
