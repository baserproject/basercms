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

use BaserCore\Mailer\TwoFactorAuthenticationMailer;
use BaserCore\Model\Table\TwoFactorAuthenticationsTable;
use Cake\Core\Configure;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

/**
 * Class TwoFactorAuthenticationsService
 */
class TwoFactorAuthenticationsService implements TwoFactorAuthenticationsServiceInterface
{
    use MailerAwareTrait;

    /**
     * TwoFactorAuthentications Table
     * @var TwoFactorAuthenticationsTable
     */
    private TwoFactorAuthenticationsTable $TwoFactorAuthentications;

    /**
     * TwoFactorAuthenticationsService constructor.
     */
    public function __construct()
    {
        $this->TwoFactorAuthentications = TableRegistry::getTableLocator()->get('BaserCore.TwoFactorAuthentications');
    }

    /**
     * 認証コード送信
     *
     * @param int $userId
     * @param string $email
     * @return void
     */
    public function send(int $userId, string $email): void
    {
        $code = sprintf('%06d', mt_rand(0, 999999));

        $twoFactorAuthentication = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => $userId])
            ->first();

        if ($twoFactorAuthentication) {
            $twoFactorAuthentication->code = $code;
            $twoFactorAuthentication->is_verified = 0;
        } else {
            $twoFactorAuthentication = $this->TwoFactorAuthentications->newEntity([
                'user_id' => $userId,
                'code' => $code,
            ]);
        }
        $this->TwoFactorAuthentications->saveOrFail($twoFactorAuthentication);

        $this->getMailer(TwoFactorAuthenticationMailer::class)
             ->send('sendCode', [$email, $twoFactorAuthentication->code]);
    }

    /**
     * 認証コード検証
     *
     * @param int $userId
     * @param string $code
     * @return bool
     */
    public function verify(int $userId, string $code): bool
    {
        if (!$userId || !$code) {
            return false;
        }

        $expire = time() - (Configure::read('BcApp.twoFactorAuthenticationCodeAllowTime') * 60);
        $twoFactorAuthentication = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => $userId])
            ->where(['code' => $code])
            ->where(['is_verified' => 0])
            ->where(['modified >=' => date('Y-m-d H:i:s', $expire)])
            ->first();
        if (!$twoFactorAuthentication) {
            return false;
        }
        $twoFactorAuthentication->is_verified = 1;
        $this->TwoFactorAuthentications->saveOrFail($twoFactorAuthentication);
        return true;
    }
}
