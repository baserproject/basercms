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
 * users login form
 */
const usersLoginForm = {

    /**
     * 初期化
     */
    mounted() {
        this.initView();
    },

    /**
     * 表示初期化
     */
    initView() {
        let isEnableLoginCredit = $("#AdminUsersLoginScript").attr('data-isEnableLoginCredit');
        if (isEnableLoginCredit) {
            let $body = $("body").hide();
            let $logo = $("#Logo");
            $body.append($("<div>&nbsp;</div>").attr('id', 'Credit').show());
            $("#HeaderInner").css('height', '50px');
            $logo.css('position', 'absolute');
            $logo.css('z-index', '10000');
            this.changeView(isEnableLoginCredit);
            // 本体がない場合にフッターが上にあがってしまうので一旦消してから表示
            $body.fadeIn(50);
        }
        this.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
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

        $("#BtnLogin").click(function(){
            $.bcUtil.showLoader();
        });
    },

    /**
     * エンドロールの表示を切り替える
     * @param creditOn
     */
    changeView(creditOn) {
        if (creditOn) {
            $.bcCredit.show();
        } else {
            this.openCredit();
        }
    },

    /**
     * エンドロールを表示する
     * @param completeHandler
     */
    openCredit(completeHandler) {
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
};

usersLoginForm.mounted();
