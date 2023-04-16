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

namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SiteConfigValidation
 */
class SiteConfigValidation extends Validation
{

    /**
     * SSL用のURLが設定されているかチェックする
     * @param mixed $check
     * @return boolean
     */
    public static function sslUrlExists($adminSsl, $context)
    {
        if ($adminSsl && empty($context['data']['ssl_url'])) {
            return false;
        }
        return true;
    }

}
