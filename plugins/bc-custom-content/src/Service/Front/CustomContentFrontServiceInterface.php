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

namespace BcCustomContent\Service\Front;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Controller\Controller;

interface CustomContentFrontServiceInterface
{

    /**
     * カスタムエントリーの詳細ページ用のプレビューのセットアップを行う
     *
     * @param Controller $controller
     */
    public function setupPreviewForView(Controller $controller): void;

}
