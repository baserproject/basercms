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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcFrontContentsService
 *
 * コンテンツ管理を利用しているコンテンツについて、
 * フロントエンドで利用する変数を生成するためのサービス
 */
interface BcFrontContentsServiceInterface
{

    /**
     * フロント用の view 変数を取得する
     * @param $content
     * @return array
     * @noTodo
     * @checked
     */
    public function getViewVarsForFront($content): array;

}
