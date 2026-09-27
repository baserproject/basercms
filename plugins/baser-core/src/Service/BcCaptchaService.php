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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcCaptchaService
 */
class BcCaptchaService implements BcCaptchaServiceInterface
{

    /**
     * キャプチャ画象を表示する
     *
     * @return void
     * @checked
     * @noTodo
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function check(ServerRequest $request, string $token, string $value): bool
    {
        require_once BASER_VENDORS . 'kcaptcha/kcaptcha_config.php';
        $key = 'captcha.' . $token;
        $expected = $request->getSession()->read($key);
        return ($value !== '' && is_string($expected) && $value === $expected);
    }

    /**
     * 認証の回答をセッションから破棄する
     *
     * 送信完了後に呼び出し、同じ回答の再利用（リプレイ）を防ぐ。
     * 保存に失敗した場合は呼び出さず、利用者が再入力せずにやり直せるようにする
     *
     * @param ServerRequest $request
     * @param string $token
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function consume(ServerRequest $request, string $token): void
    {
        $request->getSession()->delete('captcha.' . $token);
    }

}
