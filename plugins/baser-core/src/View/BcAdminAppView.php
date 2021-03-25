<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View;

/**
 * Class BcAdminAppView
 * @package BaserCore\View
 */
class BcAdminAppView extends AppView
{
    /**
     * initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BaserCore.BcAdminForm', ['templates' => 'BaserCore.bc_form']);
        $this->loadHelper('BaserCore.BcBaser');
        $this->loadHelper('BaserCore.BcAuth');
        $this->loadHelper('BaserCore.BcAdmin');
        if (!$this->get('title')) {
            $this->set('title', 'Undefined');
        }
    }
}
