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

namespace BcInstaller\Mailer\Admin;

use BaserCore\Mailer\Admin\BcAdminMailer;
use BaserCore\Model\Entity\PasswordRequest;
use BaserCore\Model\Entity\User;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\Routing\Router;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class InstallerMailer
 */
class InstallerMailer extends BcAdminMailer
{

    /**
     * パスワード再発行URLメール送信
     * @param User|EntityInterface $user
     * @param PasswordRequest|EntityInterface
     * @checked
     * @noTodo
     */
    public function installed(string $email)
    {
        $this->setTo($email)
            ->setSubject(__d('baser', 'baserCMSインストール完了'))
            ->viewBuilder()
            ->setTemplate('installed')
            ->setVars([
                'email' => $email,
                'siteUrl' => BcUtil::siteUrl(),
                'adminUrl' => Router::url([
                    'prefix' => 'Admin',
                    'plugin' => 'BaserCore',
                    'controller' =>
                    'Users',
                    'action' => 'login'
                ], true)
            ]);
    }

}
