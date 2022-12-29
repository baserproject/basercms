/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {

    let alertMessage = $("#AlertMessage");
    let isEnableLoginCredit = $("#AdminUsersLoginScript").attr('data-isEnableLoginCredit');

    if (isEnableLoginCredit) {
        $("body").hide();
    }

    if (isEnableLoginCredit) {
        let $body = $("body");
        let $logo = $("#Logo");
        $body.append($("<div>&nbsp;</div>").attr('id', 'Credit').show());
        $("#HeaderInner").css('height', '50px');
        $logo.css('position', 'absolute');
        $logo.css('z-index', '10000');
        changeView(isEnableLoginCredit);
        // 本体がない場合にフッターが上にあがってしまうので一旦消してから表示
        $body.fadeIn(50);
    }

    /**
     * ログイン実行
     */
    $("#BtnLogin").click(function () {
        $.bcUtil.showLoader()
        alertMessage.fadeOut()
        $.bcJwt.login(
            $('#email').val(),
            $('#password').val(),
            $('#saved').prop('checked'),
            function (response) {
                let query = decodeURIComponent(location.search).replace('?', '').split('&');
                let redirect
                query.forEach(function (v) {
                    let [key, value] = v.split('=')
                    if (key === 'redirect') {
                        redirect = value
                    }
                });
                if (redirect) {
                    location.href = $.bcUtil.baseUrl + redirect
                } else {
                    location.href = response.redirect
                }
            }, function () {
                alertMessage.fadeIn()
                $.bcUtil.hideLoader()
            }
        )
        return false;
    });

    /**
     * ログインエリア周辺クリック時
     * エンドロールを非表示にする
     */
    $("#Login").click(function () {
        changeView(false);
    });

    /**
     * ログインエリア内側クリック時
     * エンドロールの非表示を無効にする
     */
    $("#LoginInner").click(function (e) {
        if (e && e.stopPropagation) {
            e.stopPropagation();
        } else {
            window.event.cancelBubble = true;
        }
    });

    /**
     * エンドロールの表示を切り替える
     * @param creditOn
     */
    function changeView(creditOn) {
        if (creditOn) {
            $.bcCredit.show();
        } else {
            openCredit();
        }
    }

    /**
     * エンドロールを表示する
     * @param completeHandler
     */
    function openCredit(completeHandler) {
        let $credit = $("#Credit");
        let $logo = $("#Logo");
        if (!$credit.length) {
            return;
        }
        $("#HeaderInner").css('height', 'auto');
        $logo.css('position', 'relative');
        $logo.css('z-index', '0');
        $("#Wrap").css('height', '280px');
        if (completeHandler) {
            if ($credit.length) {
                $credit.fadeOut(1000, completeHandler);
            }
            completeHandler();
        } else {
            if ($credit.length) {
                $credit.fadeOut(1000);
            }
        }
    }

});
