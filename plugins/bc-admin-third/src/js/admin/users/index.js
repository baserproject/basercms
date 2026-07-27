/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * ユーザー一覧
 */
$(function () {
    /**
     * 代理ログインボタンの二重クリック防止
     */
    $('.btn-login').on('click', function (e) {
        if ($(this).attr('disabled')) {
            e.preventDefault();
            return false;
        }
        $(this).attr('disabled', 'disabled');
    });
});
