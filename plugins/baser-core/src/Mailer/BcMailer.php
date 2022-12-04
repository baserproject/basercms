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

namespace BaserCore\Mailer;

use BaserCore\Utility\BcSiteConfig;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;

/**
 * Class BcMailer
 * @package BaserCore\Mailer
 */
class BcMailer extends Mailer
{

    /**
     * Constructor
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        $request = Router::getRequest();
        $site = $request->getAttribute('currentSite');
        if($site) $this->viewBuilder()
            ->setTheme($site->theme)
            ->setClassName('BaserCore.BcFrontEmail');
        $this->setFrom([
            BcSiteConfig::get('email') => BcSiteConfig::get('formal_name')
        ]);
    }
}
