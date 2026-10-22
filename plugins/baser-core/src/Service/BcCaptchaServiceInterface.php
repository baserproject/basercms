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
     * キャプチャ画像を生成して画像データを返す
     *
     * @return string 画像のバイナリデータ
     */
    public function render(ServerRequest $request, string $token): string;

    /**
     * 認証を行う
     *
     * @param string $value フォームから送信された文字列
     * @return bool
     */
    public function check(ServerRequest $request, string $token, string $value): bool;

    /**
     * 認証の回答をセッションから破棄する
     *
     * 送信完了後に呼び出し、同一セッションでの回答の再利用（リプレイ）を防ぐ
     *
     * @param ServerRequest $request
     * @param string $token
     * @return void
     */
    public function consume(ServerRequest $request, string $token): void;

}
