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

namespace BaserCore\View;

use BaserCore\View\Helper\BcHtmlHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\View\View;

/**
 * BcFrontAppView
 *
 * フロントページで利用する Email で利用する
 *
 * @uses BcFrontAppView
 * @property BcHtmlHelper $BcHtml
 */
class BcFrontEmailView extends View
{

    /**
     * initialize
     *
     * @checked
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BaserCore.BcHtml');
    }

}
