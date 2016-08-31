$(function(){
	$('input[name="data[MailContent][sender_1_]"]').click(mailContentSender1ClickHandler);
	$("#MailContentSender1").hide();

	if($('input[name="data[MailContent][sender_1_]"]:checked').val()===undefined){
		if($("#MailContentSender1").val()!=''){
			$("#MailContentSender11").prop('checked',true);
		}else{
			$("#MailContentSender10").prop('checked',true);
		}
	}
	$("#EditLayout").click(function(){
		if(confirm('メールフォーム設定を保存して、レイアウトテンプレート '+$("#MailContentLayoutTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#MailContentEditLayout").val(1);
			$("#MailContentEditMailForm").val('');
			$("#MailContentEditMail").val('');
			$("#MailContentAdminEditForm").submit();
		}
	});
	$("#EditForm").click(function(){
		if(confirm('メールフォーム設定を保存して、メールフォームテンプレート '+$("#MailContentFormTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#MailContentEditLayout").val('');
			$("#MailContentEditMailForm").val(1);
			$("#MailContentEditMail").val('');
			$("#MailContentAdminEditForm").submit();
		}
	});
	$("#EditMail").click(function(){
		if(confirm('メールフォーム設定を保存して、送信メールテンプレート '+$("#MailContentMailTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#MailContentEditLayout").val('');
			$("#MailContentEditMailForm").val('');
			$("#MailContentEditMail").val(1);
			$("#MailContentAdminEditForm").submit();
		}
	});
	mailContentSender1ClickHandler();
});

function mailContentSender1ClickHandler(){
	if($('input[name="data[MailContent][sender_1_]"]:checked').val() == '1'){
		$("#MailContentSender1").slideDown(100);
	}else{
		$("#MailContentSender1").slideUp(100);
	}
}
