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

namespace BcCustomContent\View;

use BaserCore\View\BcAdminAppView;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class CustomContentAdminAppView
 * @property CustomContentAdminHelper $CustomContentAdmin
 */
class CustomContentAdminAppView extends BcAdminAppView
{

    /**
     * initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->addHelper('BcCustomContent.CustomContentAdmin');
    }

}
