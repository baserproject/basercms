<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcSample;

use BaserCore\BcPlugin;

/**
 * plugin for BcSample
 */
class Plugin extends BcPlugin
{

    /**
     * Install
     *
     * @param array $options
     * @return bool
     */
    public function install($options = []) : bool
    {
        return parent::install();
    }

}
