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

use BaserCore\Error\BcException;
use BaserCore\Mailer\TwoFactorAuthenticationMailer;
use BaserCore\Model\Table\TwoFactorAuthenticationsTable;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
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
    public function send(int $userId, string $email, bool $enforceCooldown = false): void
    {
        $twoFactorAuthentication = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => $userId])
            ->first();

        // 再送信のクールダウン（総当たり・スパム対策）。
        // 初回送信（パスワード認証直後）では強制せず、再送信・send_code のみ対象とする。
        if ($enforceCooldown && $twoFactorAuthentication && $twoFactorAuthentication->modified) {
            $cooldown = (int)Configure::read('BcApp.twoFactorAuthenticationResendCooldown', 60);
            if ($cooldown > 0 && $twoFactorAuthentication->modified->addSeconds($cooldown)->isFuture()) {
                throw new BcException(__d('baser_core', '認証コードの再送信は{0}秒以上の間隔を空けてください。', $cooldown));
            }
        }

        $code = sprintf('%06d', mt_rand(0, 999999));

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

        $allowTime = (int)Configure::read('BcApp.twoFactorAuthenticationCodeAllowTime', 10);
        if ($allowTime < 1) {
            $allowTime = 10;
        }
        $expire = FrozenTime::now()->subMinutes($allowTime);
        $twoFactorAuthentication = $this->TwoFactorAuthentications->find()
            ->where(['user_id' => $userId])
            ->where(['code' => $code])
            ->where(['is_verified' => 0])
            ->where(['modified >=' => $expire])
            ->first();
        if (!$twoFactorAuthentication) {
            // 総当たり対策: 検証に失敗した時点で、そのユーザーの有効なコードを無効化する。
            // 以降は再送信しない限り認証できないため、有効期限内の総当たりを防げる。
            $this->TwoFactorAuthentications->updateAll(
                ['is_verified' => true],
                ['user_id' => $userId, 'is_verified' => false]
            );
            return false;
        }
        $twoFactorAuthentication->is_verified = 1;
        $this->TwoFactorAuthentications->saveOrFail($twoFactorAuthentication);
        return true;
    }
}
