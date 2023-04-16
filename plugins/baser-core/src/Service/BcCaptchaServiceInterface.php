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

/**
 * interface BcCaptchaServiceInterface
 */
interface BcCaptchaServiceInterface
{

    /**
     * キャプチャ画象を表示する
     *
     * @return void
     */
    public function render(ServerRequest $request, string $token): void;

    /**
     * 認証を行う
     *
     * @param string $value フォームから送信された文字列
     * @return bool
     */
    public function check(ServerRequest $request, string $token, string $value): bool;

}
