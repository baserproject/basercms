/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

loadAuthCaptcha();
$("#BlogCommentAddButton").on('click', function () {
    sendComment();
    return false;
});

var alertMessageName = $("#BlogCommentsScripts").attr('data-alertMessageName');
var alertMessageComment = $("#BlogCommentsScripts").attr('data-alertMessageComment');
var alertMessageAuthImage = $("#BlogCommentsScripts").attr('data-alertMessageAuthImage');
var alertMessageAuthComplate = $("#BlogCommentsScripts").attr('data-alertMessageAuthComplate');
var alertMessageComplate = $("#BlogCommentsScripts").attr('data-alertMessageComplate');
var alertMessageError = $("#BlogCommentsScripts").attr('data-alertMessageError');


//コメントの承認あり・なしの初期値
var commentApprove = false;

/**
 * コメントを送信する
 */
function sendComment() {
    let msg = '';

    $.bcToken.setTokenUrl('/blog/blog_comments/get_token');

    if (!$("#BlogCommentName").val()) {
        msg += alertMessageName + '\n';
    }
    if (!$("#BlogCommentMessage").val()) {
        msg += alertMessageComment + '\n';
    }
    if (authCaptcha) {
        if (!$("#BlogCommentAuthCaptcha").val()) {
            msg += alertMessageAuthImage + '\n';
        }
    }
    if (msg) {
        alert(msg);
        return;
    }

    $.bcToken.check(function () {
        let form = $("#BlogCommentAddForm");
        form.find('input[name="data[_Token][key]"]').remove();
        form.append($.bcToken.getHiddenToken());
        return $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'html',
            beforeSend: function () {
                $("#BlogCommentAddButton").prop('disabled', true);
                $("#ResultMessage").slideUp();
            },
            success: function (result) {
                if (result) {
                    loadAuthCaptcha();
                    $("#BlogCommentName").val('');
                    $("#BlogCommentEmail").val('');
                    $("#BlogCommentUrl").val('');
                    $("#BlogCommentMessage").val('');
                    $("#BlogCommentAuthCaptcha").val('');
                    let resultMessage = '';
                    if (commentApprove) {
                        resultMessage = alertMessageAuthComplate;
                    } else {
                        let comment = $(result);
                        comment.hide();
                        $("#BlogCommentList").append(comment);
                        comment.show(500);
                        resultMessage = alertMessageComplate;
                    }
                    $("#ResultMessage").html(resultMessage);
                    $("#ResultMessage").slideDown();
                } else {
                    postError();
                }
            },
            error: function (result) {
                postError();
            },
            complete: function (xhr, textStatus) {
                $.bcToken.key = null;
                $("#BlogCommentAddButton").removeAttr('disabled');
            }
        });
    }, {useUpdate: false});
}

/**
 * エラー処理
 */
function postError() {
    loadAuthCaptcha();
    $("#BlogCommentAuthCaptcha").val('');
    $("#ResultMessage").html(alertMessageError);
    $("#ResultMessage").slideDown();
}

/**
 * キャプチャ画像を読み込む
 */
function loadAuthCaptcha() {
    if (authCaptcha) {
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
