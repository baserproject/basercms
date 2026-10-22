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
     * キャプチャのベースURL
     */
    authCaptchaImageBaseUrl: null,

    /**
     * 初期化
     */
    mounted() {
        const $script = $("#BlogCommentsScripts");
        bcBlog.commentApprove = $script.attr('data-commentApprove');
        bcBlog.authCaptcha = $script.attr('data-authCaptcha');
        bcBlog.captchaId = $script.attr('data-captchaId');
        bcBlog.authCaptchaImageBaseUrl = $script.attr('data-authCaptchaImageBaseUrl');
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
                    if(bcBlog.authCaptcha) {
                        bcBlog.loadAuthCaptcha();
                        $("#auth-captcha").val('');
                    }
                    if (result) {
                        bcBlog.initView()
                        $("#auth-captcha").val('');
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
     * エラー処理
     */
    postError(result) {
        if(bcBlog.authCaptcha) {
            bcBlog.loadAuthCaptcha();
            $("#auth-captcha").val('');
        }
        // サーバーの応答は HTML として描画せず、エスケープしてから表示する
        let message = bcI18n.alertMessageError;
        let response = {};
        if(result && result.responseText) {
            try {
                response = JSON.parse(result.responseText) || {};
            } catch (e) {
                response = {};
            }
        }
        if(response.message) {
            message += '<br>' + $.bcUtil.escapeHtml(response.message);
        }
        if(response.errors) {
            Object.keys(response.errors).forEach(function(k) {
                Object.keys(response.errors[k]).forEach(function(f){
                    message += '<br>' + $.bcUtil.escapeHtml(response.errors[k][f]);
                });
            });
        }
        $("#ResultMessage").html(message).slideDown();
    },

    /**
     * バリデーション
     */
    validate() {
        let msg = '';
        if (!$("#name").val()) {
            msg += bcI18n.alertMessageName + '\n';
        }
        if (!$("#email").val()) {
            msg += bcI18n.alertMessageEmail + '\n';
        }
        if (!$("#message").val()) {
            msg += bcI18n.alertMessageComment + '\n';
        }
        if (bcBlog.authCaptcha) {
            if (!$("#auth-captcha").val()) {
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
     * キャプチャ画像を読み込む
     */
    loadAuthCaptcha() {
        if (bcBlog.authCaptcha) {
            const $authCaptchaImage = $("#AuthCaptchaImage");
            const $captchaLoader = $("#CaptchaLoader");
            $authCaptchaImage.hide();
            $captchaLoader.show();
            // 呼び出しのたびにハンドラが重複しないよう one を使う
            $authCaptchaImage.one('load', function () {
                $captchaLoader.hide();
                $authCaptchaImage.fadeIn(1000);
            });
            // 画像認証の回答は投稿に成功するとサーバー側で破棄されるため、
            // キャッシュを使わず毎回新しい画像（新しい回答）を取得する
            $authCaptchaImage.attr('src', bcBlog.authCaptchaImageBaseUrl + '/' + bcBlog.captchaId + '?t=' + Date.now());
        }
    }

}

bcBlog.mounted();





