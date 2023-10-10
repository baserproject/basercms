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

namespace BaserCore\Controller;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メンテナンスコントローラー
 */
class MaintenanceController extends BcFrontAppController
{

    /**
     * メンテナンス中ページを表示する
     * @checked
     * @noTodo
     */
    public function index()
    {
        $this->getResponse()->withStatus(503);
        $this->setTitle( __d('baser_core', 'メンテナンス中'));
    }

}
