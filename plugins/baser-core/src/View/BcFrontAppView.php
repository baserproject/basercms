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

use BaserCore\View\Helper\BcTextHelper;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * BcFrontAppView
 * @uses BcFrontAppView
 * @property BcTextHelper $BcText
 */
class BcFrontAppView extends AppView
{

    /**
     * initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        if (!empty($this->getRequest()->getAttribute('currentSite')->device)) {
            $agentHelper = Configure::read('BcAgent.' . $this->getRequest()->getAttribute('currentSite')->device . '.helper');
            if ($agentHelper) $this->loadHelper($agentHelper);
        }
        $this->loadHelper('BaserCore.BcText');
    }

}
