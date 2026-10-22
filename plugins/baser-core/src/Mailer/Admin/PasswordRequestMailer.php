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
     * @unitTest
     */
    public function resetPassword(EntityInterface $user, EntityInterface $passwordRequest)
    {
        $subject = __d('baser_core', 'パスワード再発行');
        $passwordRequestData = $passwordRequest->toArray();
        $createtime = $passwordRequestData['created']->timestamp;
        $agoInStr = '+' . Configure::read('BcApp.passwordRequestAllowTime') . ' min';
        $timelimit = date('Y/m/d H:i', strtotime($agoInStr, $createtime));
        $current = Router::getRequest();
        $urlArray = [
            'plugin' => 'BaserCore',
            'prefix' => $current->getParam('prefix'),
            'controller' => 'password_requests',
            'action' => 'apply',
            $passwordRequestData['request_key'],
        ];
        // リセット URL のホストはリクエストの Host ヘッダではなく、設定済みのサイト URL（SITE_URL）から
        // 生成する。Host ヘッダ由来だと、細工した Host を付けた再発行リクエストで攻撃者のドメインへ
        // 誘導する URL がメールに記載され、リセットトークンを窃取される（Host ヘッダインジェクション）
        $siteUrl = (string)Configure::read('BcEnv.siteUrl');
        if ($siteUrl && preg_match('/^https?:\/\//', $siteUrl)) {
            $path = Router::url($urlArray);
            $base = (string)$current->getAttribute('base');
            if ($base && str_starts_with($path, $base)) {
                $path = substr($path, strlen($base));
            }
            $url = rtrim($siteUrl, '/') . '/' . ltrim($path, '/');
        } else {
            $url = Router::url($urlArray, true);
        }

        $this->setTo($user->email)
            ->setSubject($subject)
            ->viewBuilder()
            ->setTemplate('password_request')
            ->setVars(['limit' => $timelimit, 'url' => $url]);
    }

}
