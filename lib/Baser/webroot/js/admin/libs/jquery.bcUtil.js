(function($){
	$.bcUtil = {
		showAlertMessage: function(message) {
			$.bcUtil.hideMessage();
			$("#BcSystemMessage")
				.removeClass('notice-messge alert-message')
				.addClass('alert-message')
				.html(message);
			$("#BcMessageBox").fadeIn(500);
		},
		showNoticeMessage: function(message) {
			$.bcUtil.hideMessage();
			$("#BcSystemMessage")
				.removeClass('notice-messge alert-message')
				.addClass('notice-message')
				.html(message);
			$("#BcMessageBox").fadeIn(500);
		},
		hideMessage: function() {
			$("#BcMessageBox").fadeOut(200);
			$("#AlertMessage").fadeOut(200);
			$("#MessageBox").fadeOut(200);
		},
		showLoader: function() {
			$("#Waiting").show();
		},
		hideLoader: function() {
			$("#Waiting").hide();
		},
		/**
		 * Ajax のエラーメッセージを表示
		 *
		 * @param XMLHttpRequest
		 * @param errorThrown
		 * @param message
		 */
		showAjaxError: function (message, XMLHttpRequest, errorThrown) {
			var errorMessage = '';
			if (XMLHttpRequest !== undefined && XMLHttpRequest.status) {
				errorMessage = '<br />(' + XMLHttpRequest.status + ') ';
			}
			if (XMLHttpRequest !== undefined && XMLHttpRequest.responseText) {
				errorMessage += XMLHttpRequest.responseText;
			} else if (errorThrown !== undefined) {
				errorMessage += errorThrown;
			}
			$.bcUtil.showAlertMessage(message + errorMessage);
		}
	};
})(jQuery);