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

/**
 * Class TwoFactorAuthenticationMailer
 */
class TwoFactorAuthenticationMailer extends BcMailer
{

    /**
     * 認証コード送信
     *
     * @param string $email
     * @param string $code
     */
    public function sendCode(string $email, string $code)
    {
        $this->setTo($email)
            ->setSubject('認証コードのお知らせ')
            ->viewBuilder()
            ->setTemplate('login_code')
            ->setVars(['code' => $code]);
    }

}
