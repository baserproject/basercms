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

use Cake\Http\ServerRequest;
use KCAPTCHA;

/**
 * Class BcCaptchaService
 */
class BcCaptchaService implements BcCaptchaServiceInterface
{

    /**
     * キャプチャ画象を表示する
     *
     * @return void
     */
    public function render(ServerRequest $request, string $token): void
    {
        require_once BASER_VENDORS . 'kcaptcha/kcaptcha.php';
        $kcaptcha = new KCAPTCHA();
        $key = 'captcha.' . $token;
        $request->getSession()->write($key, $kcaptcha->getKeyString());
    }

    /**
     * 認証を行う
     *
     * @param string $value フォームから送信された文字列
     * @return bool
     */
    public function check(ServerRequest $request, string $token, string $value): bool
    {
        require_once BASER_VENDORS . 'kcaptcha/kcaptcha_config.php';
        $key = 'captcha.' . $token;
        return ($value === $request->getSession()->read($key));
    }

}
