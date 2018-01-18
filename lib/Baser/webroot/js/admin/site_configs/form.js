/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


$(function(){
	var safeModeOn = $("#AdminSiteConfigsFormScript").attr('data-safeModeOn');
	var isAdminSsl = $("#AdminSiteConfigsFormScript").attr('data-isAdminSsl');
	/**
	 * 「保存」ボタンを押下した際の動作
	 */
	$("#BtnSave").click(function(){
		if (!isSafeModeCheck()) {
			return false;
		}
		if (!isAdminSslCheck()) {
			return false;
		}
		$.bcUtil.showLoader();
	});

	function isAdminSslCheck() {
		if(isAdminSsl == "0" && $("input[name='data[SiteConfig][admin_ssl]']:checked").val() == "1") {
			if(!$("#SiteConfigSslUrl").val()) {
				alert('管理システムをSSLに切り替える場合には、SSL用のURLを登録してください。');
				window.location.hash = 'SiteConfigSslUrl';
				return false;
			}
			var adminSslAlert = '管理システムをSSLに切り替えようとしています。よろしいですか？<br><br>'+
				'サーバがSSLに対応していない場合、管理システムを表示する事ができなくなってしまいますのでご注意ください。<br><br>'+
				'もし、表示する事ができなくなってしまった場合は、 /app/Config/install.php の、 BcEnv.sslUrl の値を調整するか、BcApp.adminSsl の値を false に書き換えて復旧してください。';
			$.bcConfirm.show({
				title			: '管理システムSSL設定確認',
				message			: adminSslAlert,
				defaultCancel	: true,
				ok				: function(){
					$.bcUtil.showLoader();
					$("#SiteConfigFormForm").submit();
				}
			});
			return false;
		}
		return true;
	}

	function isSafeModeCheck() {
		var theme = $("#SiteConfigTheme").val();
		var safemodeAlert = '機能制限のセーフモードで動作しています。テーマの切り替えを行う場合、あらかじめ切り替え対象のテーマ内に、データベースに登録されているページカテゴリ用のフォルダを作成しておき、書込権限を与えておく必要があります。\n'+
			'ページカテゴリ用のフォルダが存在しない状態でテーマの切り替えを実行すると、対象ページカテゴリ内のWebページは正常に表示できなくなりますのでご注意ください。';

		if(safeModeOn && (theme != $("#SiteConfigTheme").val())) {
			if(!confirm(safemodeAlert)) {
				return false;
			}
		}
		return true;
	}

	// SMTP送信テスト
	$("#BtnCheckSendmail").click(function(){
		if(!confirm('テストメールを送信します。いいですか？')) {
			return false;
		}
		$.bcToken.check(function(){
			return $.ajax({
				type: 'POST',
				url: $.baseUrl + '/' + $.bcUtil.adminPrefix + '/site_configs/check_sendmail',
				data: $("#SiteConfigFormForm").serialize(),
				beforeSend: function() {
					$("#ResultCheckSendmail").hide();
					$("#AjaxLoaderCheckSendmail").show();
				},
				success: function(result){
					$("#ResultCheckSendmail").html("テストメールを送信しました。");
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					var errorMessage = '';
					if(XMLHttpRequest.responseText) {
						errorMessage = XMLHttpRequest.responseText;
					} else {
						errorMessage = errorThrown;
					}
					$("#ResultCheckSendmail").html("テストメールを送信に失敗しました。" + errorMessage);
				},
				complete: function() {
					$("#ResultCheckSendmail").show();
					$("#AjaxLoaderCheckSendmail").hide();
				}
			});
		}, {loaderType: 'none'});
		return false;
	});

	$("#SiteConfigMobile").click(function(){
		if($("#SiteConfigMobile").prop('checked')) {
			$("#SpanLinkedPagesMobile").show();
			$("#SpanRootLayoutTemplateMobile").show();
			$("#SpanRootContentTemplateMobile").show();
		} else {
			$("#SpanLinkedPagesMobile").hide();
			$("#SpanRootLayoutTemplateMobile").hide();
			$("#SpanRootContentTemplateMobile").hide();
			$('#SiteConfigLinkedPagesMobile0').prop('checked', 'checked');
		}
	});
	$("#SiteConfigSmartphone").click(function(){
		if($("#SiteConfigSmartphone").prop('checked')) {
			$("#SpanLinkedPagesSmartphone").show();
			$("#SpanRootLayoutTemplateSmartphone").show();
			$("#SpanRootContentTemplateSmartphone").show();
		} else {
			$("#SpanLinkedPagesSmartphone").hide();
			$("#SpanRootLayoutTemplateSmartphone").hide();
			$("#SpanRootContentTemplateSmartphone").hide();
			$('#SiteConfigLinkedPagesSmartphone0').prop('checked', 'checked');
		}
	});

	$('input[name="data[SiteConfig][editor]"]').click(siteConfigEditorClickHandler);

	if(!$("#SiteConfigMobile").prop('checked')) {
		$("#SpanLinkedPagesMobile").hide();
		$("#SpanRootLayoutTemplateMobile").hide();
		$("#SpanRootContentTemplateMobile").hide();
	}
	if(!$("#SiteConfigSmartphone").prop('checked')) {
		$("#SpanLinkedPagesSmartphone").hide();
		$("#SpanRootLayoutTemplateSmartphone").hide();
		$("#SpanRootContentTemplateSmartphone").hide();
	}

	siteConfigEditorClickHandler();

	function siteConfigEditorClickHandler() {
		if($('input[name="data[SiteConfig][editor]"]:checked').val() === 'BcCkeditor') {
			$(".ckeditor-option").show();
		} else {
			$(".ckeditor-option").hide();
		}
	}

});
