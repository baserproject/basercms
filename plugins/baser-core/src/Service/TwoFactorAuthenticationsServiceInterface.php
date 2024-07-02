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

namespace BaserCore\Service;

/**
 * Interface TwoFactorAuthenticationsServiceInterface
 */
interface TwoFactorAuthenticationsServiceInterface
{
    /**
     * 認証コード送信
     *
     * @param int $userId
     * @param string $email
     */
    public function send(int $userId, string $email): void;

    /**
     * 認証コード検証
     *
     * @param int $userId
     * @param string $code
     */
    public function verify(int $userId, string $code): bool;
}
