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
 * ブログコメント送信
 */
const bcBlog = {

    /**
     * コメントの承認あり・なし
     */
    commentApprove: false,

    /**
     * キャプチャ認証を利用するかどうか
     */
    authCaptcha: false,

    /**
     * キャプチャID
     */
    captchaId: null,

    /**
     * 初期化
     */
    mounted() {
        const $script = $("#BlogCommentsScripts");
        bcBlog.commentApprove = $script.attr('data-commentApprove');
        bcBlog.authCaptcha = $script.attr('data-authCaptcha');
        bcBlog.captchaId = $script.attr('data-captchaId');
        bcBlog.initView();
        bcBlog.registerEvents();
    },

    /**
     * イベント登録
     */
    registerEvents() {
        $("#BlogCommentAddButton").on('click', function () {
            if(bcBlog.validate()) {
                bcBlog.sendComment();
            }
            return false;
        });
    },

    /**
     * 表示を初期化する
     */
    initView() {
        if(bcBlog.authCaptcha) bcBlog.loadAuthCaptcha();
        $("#name").val('');
        $("#email").val('');
        $("#url").val('');
        $("#message").val('');
    },

    /**
     * コメントを送信する
     */
    sendComment() {
        $("#BlogCommentAddButton").prop('disabled', true);
        $.bcToken.check(function () {
            let form = $("#BlogCommentAddForm");
            form.find('input[name="_csrfToken"]').remove();
            form.append($.bcToken.getHiddenToken());
            return $.ajax({
                url: $.bcUtil.baseUrl + '/bc-blog/blog/ajax_add_comment',
                type: 'POST',
                data: form.serialize(),
                dataType: 'html',
                beforeSend: function () {
                    $("#ResultMessage").slideUp();
                },
                success: function (result) {
                    if (result) {
                        bcBlog.initView()
                        $("#BlogCommentAuthCaptcha").val('');
                        let resultMessage = '';
                        if (bcBlog.commentApprove) {
                            resultMessage = bcI18n.alertMessageAuthComplete;
                            $("#ResultMessage").html(resultMessage).show();
                        } else {
                            let comment = $(result);
                            comment.hide();
                            $("#BlogCommentList").append(comment);
                            comment.slideDown(500);
                            location.hash = comment.attr('id');
                        }
                    } else {
                        bcBlog.postError();
                    }
                },
                error: function (result) {
                    bcBlog.postError(result);
                },
                complete: function () {
                    $.bcToken.key = null;
                    $("#BlogCommentAddButton").removeAttr('disabled');
                }
            });
        }, {useUpdate: false});
    },

    /**
     * バリデーション
     */
    validate() {
        let msg = '';
        if (!$("#name").val()) {
            msg += bcI18n.alertMessageName + '\n';
        }
        if (!$("#message").val()) {
            msg += bcI18n.alertMessageComment + '\n';
        }
        if (bcBlog.authCaptcha) {
            if (!$("#auth_captcha").val()) {
                msg += bcI18n.alertMessageAuthImage + '\n';
            }
        }
        if (msg) {
            alert(msg);
            return false;
        }
        return true;
    },

    /**
     * エラー処理
     */
    postError(result) {
        if(bcBlog.authCaptcha) {
            bcBlog.loadAuthCaptcha();
            $("#BlogCommentAuthCaptcha").val('');
        }
        console.log(JSON.parse(result.responseText));
        $("#ResultMessage").html(bcI18n.alertMessageError).slideDown();
    },

    /**
     * キャプチャ画像を読み込む
     */
    loadAuthCaptcha() {
        if (bcBlog.authCaptcha) {
            let captchaId = Math.floor(Math.random() * 100);
            let src = $("#BlogCommentCaptchaUrl").html() + '?' + captchaId;
            $("#AuthCaptchaImage").hide();
            $("#CaptchaLoader").show();
            $("#AuthCaptchaImage").on('load', function () {
                $("#CaptchaLoader").hide();
                $("#AuthCaptchaImage").fadeIn(1000);
            });
            $("#AuthCaptchaImage").attr('src', src);
        }
    }

}

bcBlog.mounted();





