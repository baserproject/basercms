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
namespace BaserCore\Mailer\Admin;

use BaserCore\Mailer\BcMailer;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcAdminMailer
 */
class BcAdminMailer extends BcMailer
{

    /**
     * Constructor
     * @param null $config
     * @checked
     * @noTodo
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        $this->viewBuilder()->setTheme(BcSiteConfig::get('admin-theme'));
    }

}
