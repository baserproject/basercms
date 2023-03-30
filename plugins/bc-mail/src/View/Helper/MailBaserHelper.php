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

namespace BcMail\View\Helper;

use Cake\View\Helper;

/**
 * MailBaserHelper
 *
 * テーマより利用される事を前提としたヘルパー。テーマで必要となる機能を提供する。
 *
 * @property CakeRequest $request
 */
class MailBaserHelper extends Helper
{

    /**
     * 現在のページがメールプラグインかどうかを判定する
     *
     * @return bool
     */
    public function isMail()
    {
        if (!Hash::get($this->request->params, 'Content.plugin')) {
            return false;
        }
        if (Hash::get($this->request->params, 'Content.plugin') !== 'BcMail') {
            return false;
        }
        return true;
    }
}
