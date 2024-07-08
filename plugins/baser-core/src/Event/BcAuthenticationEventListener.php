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

namespace BaserCore\Event;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\TwoFactorAuthenticationsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\Exception\HttpException;
use Cake\Http\Exception\RedirectException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Routing\Router;

/**
 * Class BcAuthenticationEventListener
 */
class BcAuthenticationEventListener implements EventListenerInterface
{
    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * implementedEvents
     * @return \string[][]
     */
    public function implementedEvents(): array
    {
        return [
            'Authentication.afterIdentify' => ['callable' => 'afterIdentify'],
        ];
    }

    /**
     * 認証後処理
     *
     * @param Event $event
     */
    public function afterIdentify(Event $event)
    {
        // 二段階認証有効時はログインに認証コードが必要
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        if (!$siteConfigsService->getValue('use_two_factor_authentication')) {
            return;
        }

        $request = Router::getRequest();
        $prefix = BcUtil::getRequestPrefix($request);
        $session = $request->getSession();
        $loginUser = BcUtil::loginUser();
        $twoFactorAuthenticationsService = $this->getService(TwoFactorAuthenticationsServiceInterface::class);

        if (!in_array($prefix, ['Api/Admin', 'Admin'])) {
            return;
        }

        // API対応
        if ($prefix === 'Api/Admin') {
            if ($request->getData('code')) {
                if ($twoFactorAuthenticationsService->verify($loginUser->id, $request->getData('code'))) {
                    return;
                }
                throw new UnauthorizedException(__d('baser', '認証コードが間違っているか有効期限切れです。'));
            } else if ($request->getData('send_code')) {
                $twoFactorAuthenticationsService->send($loginUser->id, $loginUser->email);
                throw new HttpException(__d('baser', 'メールで受信した認証コードをcodeキーの値として送信してください。'), 200);
            } else {
                throw new UnauthorizedException(__d('baser', 'send_codeキーを付与すると認証コードをメールで送信します。'));
            }
        }

        // 認証コード送信
        $twoFactorAuthenticationsService->send($loginUser->id, $loginUser->email);

        // 認証コード入力画面にリダイレクト
        $session->write('TwoFactorAuth.' . $prefix, [
            'user_id' => $loginUser->id,
            'email' => $loginUser->email,
            'saved' => $request->getData('saved'),
            'date' => date('Y-m-d H:i:s'),
        ]);
        $url = Router::url('/baser/admin/baser-core/users/login_code', true);
        $redirect = $request->getQuery('redirect');
        if ($redirect) {
            $url .= '?redirect=' . urlencode($redirect);
        }
        throw new RedirectException($url);
    }
}
