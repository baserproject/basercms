/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

var alertMessageName = $("#BlogCommentsScripts").attr('data-alertMessageName');
var alertMessageComment = $("#BlogCommentsScripts").attr('data-alertMessageComment');
var alertMessageAuthImage = $("#BlogCommentsScripts").attr('data-alertMessageAuthImage');
var alertMessageAuthComplate = $("#BlogCommentsScripts").attr('data-alertMessageAuthComplate');
var alertMessageComplate = $("#BlogCommentsScripts").attr('data-alertMessageComplate');
var alertMessageError = $("#BlogCommentsScripts").attr('data-alertMessageError');

//CAPTCHA有効・無効
var authCaptcha = false;

//コメントの承認あり・なし
var commentApprove = false;

/**
* コメントを送信する
*/
function sendComment() {
	var msg = '';

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
	if (!msg) {
		$.bcToken.check(function() {
			var form = $("#BlogCommentAddForm");
			form.find('input[name="data[_Token][key]"]').remove();
			form.append($.bcToken.getHiddenToken());
			return $.ajax({
				url: form.attr('action'),
				type: 'POST',
				data: form.serialize(),
				dataType: 'html',
				beforeSend: function() {
					$("#BlogCommentAddButton").prop('disabled', true);
					$("#ResultMessage").slideUp();
				},
				success: function(result) {
					if (result) {
						loadAuthCaptcha();
						$("#BlogCommentName").val('');
						$("#BlogCommentEmail").val('');
						$("#BlogCommentUrl").val('');
						$("#BlogCommentMessage").val('');
						$("#BlogCommentAuthCaptcha").val('');
						var resultMessage = '';
						if (commentApprove) {
							resultMessage = alertMessageAuthComplate;
						} else {
							var comment = $(result);
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
				error: function(result) {
					postError();
				},
				complete: function(xhr, textStatus) {
					$.bcToken.key = null;
					$("#BlogCommentAddButton").removeAttr('disabled');
				}
			});
		}, {useUpdate:false});
	} else {
		alert(msg);
	}
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
		var src = $("#BlogCommentCaptchaUrl").html();
		var captchaId = Math.floor(Math.random() * 100);
		var src = $("#BlogCommentCaptchaUrl").html() + '?' + captchaId;
		$("#AuthCaptchaImage").hide();
		$("#CaptchaLoader").show();
		$("#AuthCaptchaImage").on('load',function() {
			$("#CaptchaLoader").hide();
			$("#AuthCaptchaImage").fadeIn(1000);
		});
		$("#AuthCaptchaImage").attr('src', src);
	}
}
