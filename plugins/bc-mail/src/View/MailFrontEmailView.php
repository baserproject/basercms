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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\BcFrontEmailView;
use BcMail\View\Helper\MaildataHelper;
use BcMail\View\Helper\MailfieldHelper;
use BcMail\View\Helper\MailHelper;

/**
 * Class MailFrontAppView
 *
 * フロントエンドのEmail送信において利用する
 *
 * @property MailHelper $Mail
 * @property MailfieldHelper $Mailfield
 * @property MaildataHelper $Maildata
 */
class MailFrontEmailView extends BcFrontEmailView
{

    /**
     * initialize
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BcMail.Mailfield');
        $this->loadHelper('BcMail.Maildata');
    }

}
