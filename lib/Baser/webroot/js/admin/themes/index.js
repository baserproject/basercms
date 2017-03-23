$(function(){
	$.baserAjaxDataList.config.methods.copy = {
		button: '.btn-copy',
		confirm: '',
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				$.bcUtil.showLoader();
				document.location.reload();
				// ↓ 部品のみ取得できる処理をコントローラーに実装したらこちらの処理に変える
				//$.baserAjaxDataList.load(document.location.href);
			} else {
				$(config.alertBox).html('コピーに失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		}
	}
	$.baserAjaxDataList.config.methods.del = {
		button: '.btn-delete',
		confirm: 'このデータを本当に削除してもよろしいですか？\n※ 削除したデータは元に戻すことができません。',
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				$.bcUtil.showLoader();
				document.location.reload();
				// ↓ 部品のみ取得できる処理をコントローラーに実装したらこちらの処理に変える
				//$.baserAjaxDataList.load(document.location.href);
			} else {
				$(config.alertBox).html('削除に失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		}
	};
	$.baserAjaxDataList.init();

	/**
	 * 初期データ読込ボタンを押下した際の動作
	 */
	$("#BtnLoadDefaultDataPattern").click(function() {
		$.bcConfirm.show({
			'title': '初期データ読込',
			'message':'<p><strong>初期データを読み込みます。よろしいですか？</strong></p><br />' +
			'<p>※ 初期データを読み込むと現在登録されている記事データや設定は全て上書きされますのでご注意ください。<br />' +
			'※ 管理ログは読み込まれず、ユーザー情報はログインしているユーザーのみに初期化されます。</p>',
			'ok':function(){
				$.bcUtil.showLoader();
				$("#ThemeLoadDefaultDataPatternForm").submit();
			}
		});
		return false;
	});

	/**
	 * マーケットのデータを取得
	 */
	$.ajax({
		url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/themes/ajax_get_market_themes',
		type: "GET",
		success: function(result) {
			$("#BaserMarket").html(result);
		}
	});

	$( "#tabs" ).tabs();

});