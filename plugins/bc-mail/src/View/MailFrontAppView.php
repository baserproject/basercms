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

namespace BcMail\View;

use BaserCore\View\BcFrontAppView;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcMail\View\Helper\MailfieldHelper;
use BcMail\View\Helper\MailformHelper;
use BcMail\View\Helper\MailHelper;

/**
 * Class MailFrontAppView
 * @property MailHelper $Mail
 * @property MailformHelper $Mailform
 * @property MailfieldHelper $Mailfield
 */
class MailFrontAppView extends BcFrontAppView
{

    /**
     * initialize
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BcMail.Mail');
        $this->loadHelper('BcMail.Mailfield');
        $this->loadHelper('BcMail.Mailform', ['templates' => 'BaserCore.bc_form']);
    }

}
