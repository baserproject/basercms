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
     * キャプチャ画像を生成して画像データを返す
     *
     * @return string 画像のバイナリデータ
     * @checked
     * @noTodo
     */
    public function render(ServerRequest $request, string $token): string
    {
        require_once BASER_VENDORS . 'kcaptcha/kcaptcha.php';
        // KCAPTCHA はコンストラクタで画像を直接出力するため、出力バッファで取得する
        // （CakePHP 5 では直接出力するとヘッダー送信済みエラーとなるため Response 経由で返す）
        ob_start();
        $kcaptcha = new KCAPTCHA();
        $image = ob_get_clean();
        $key = 'captcha.' . $token;
        $request->getSession()->write($key, $kcaptcha->getKeyString());
        return $image;
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
        return ($value === $request->getSession()->read($key));
    }

}
