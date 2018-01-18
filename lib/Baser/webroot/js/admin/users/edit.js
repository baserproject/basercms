$(function(){
	$("#BtnSave").click(function(){
		if($("#SelfUpdate").html()) {
			if(confirm('更新内容をログイン情報に反映する為、一旦ログアウトします。よろしいですか？')) {
				$.bcUtil.showLoader();
				return true;
			}
		} else {
			$.bcUtil.showLoader();
			return true;
		}
		return false;
	});
	$("#btnSetUserGroupDefault").click(function() {
		if(!confirm('登録されている「よく使う項目」を、このユーザーが所属するユーザーグループの初期設定として登録します。よろしいですか？')) {
			return true;
		}
		var data = {};
		$("#DefaultFavorites li").each(function(i){
			data[i] ={
				'name' : $(this).find('.favorite-name').val(),
				'url' :$(this).find('.favorite-url').val()
			};
		});

		$.bcToken.check(function(){
			data = $.extend(data, {
				_Token: {
					key: $.bcToken.key
				}
			});
			return $.ajax({
				url: $("#UserGroupSetDefaultFavoritesUrl").html(),
				type: 'POST',
				data: data,
				dataType: 'html',
				beforeSend: function() {
					$("#Waiting").show();
					alertBox();
				},
				success: function(result){
					$("#ToTop a").click();
					if(result) {
						$.bcUtil.showNoticeMessage('登録されている「よく使う項目」を所属するユーザーグループの初期値として設定しました。');
					} else {
						$.bcUtil.showAlertMessage('処理に失敗しました。');
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					var errorMessage = '';
					if(XMLHttpRequest.status == 404) {
						errorMessage = '<br />'+'送信先のプログラムが見つかりません。';
					} else {
						if(XMLHttpRequest.responseText) {
							errorMessage = '<br />'+XMLHttpRequest.responseText;
						} else {
							errorMessage = '<br />'+errorThrown;
						}
					}
					$.bcUtil.showAlertMessage('処理に失敗しました。('+XMLHttpRequest.status+')'+errorMessage);
				},
				complete: function() {
					$("#Waiting").hide();
				}
			});
		}, {hideLoader: false});
	});
});