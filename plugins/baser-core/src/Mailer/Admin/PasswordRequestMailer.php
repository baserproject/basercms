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

use BaserCore\Model\Entity\PasswordRequest;
use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Core\Configure;
use Cake\Routing\Router;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PasswordRequestMailer
 */
class PasswordRequestMailer extends BcAdminMailer
{

    /**
     * パスワード再発行URLメール送信
     * @param User|EntityInterface $user
     * @param PasswordRequest|EntityInterface
     * @checked
     * @noTodo
     */
    public function resetPassword(EntityInterface $user, EntityInterface $passwordRequest)
    {
        $subject = __d('baser_core', 'パスワード再発行');
        $passwordRequestData = $passwordRequest->toArray();
        $createtime = $passwordRequestData['created']->timestamp;
        $agoInStr = '+' . Configure::read('BcApp.passwordRequestAllowTime') . ' min';
        $timelimit = date('Y/m/d H:i', strtotime($agoInStr, $createtime));
        $current = Router::getRequest();
        $url = Router::url([
            'plugin' => 'BaserCore',
            'prefix' => $current->getParam('prefix'),
            'controller' => 'password_requests',
            'action' => 'apply',
            $passwordRequestData['request_key'],
        ],true);

        $this->setTo($user->email)
            ->setSubject($subject)
            ->viewBuilder()
            ->setTemplate('password_request')
            ->setVars(['limit' => $timelimit, 'url' => $url]);
    }

}
