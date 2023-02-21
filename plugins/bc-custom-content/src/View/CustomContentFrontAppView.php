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

use BaserCore\View\AppView;
use BcCustomContent\View\Helper\CustomContentHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class CustomContentFrontAppView
 * @property CustomContentHelper $CustomContent
 */
class CustomContentFrontAppView extends AppView
{

    /**
     * initialize
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BcCustomContent.CustomContent');
    }

}
