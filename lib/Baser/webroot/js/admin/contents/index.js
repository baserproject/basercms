/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧
 */

$(function () {

	var contentsIndexSearchOpened = $("#SearchBoxOpened").html();
	
	$.bcTree.init({isAdmin: $("#AdminContentsIndexScript").attr('data-isAdmin')});

	// マウスダウンイベント
	$(window).bind("mousedown", $.bcTree.updateShiftAndCtrlOnAnchor);

	// サイト変更時
	$("#ViewSettingSiteId").change(loadView);

	// 表示変更時
	$("input[name='data[ViewSetting][list_type]']").click(loadView);
	
	// 新規追加クリック時
	$("#BtnAddContent").click($.bcTree.showMenuByOuter);

	// ドラッグ＆ドロップイベント
	$(document).on("dnd_stop.vakata", $.bcTree.orderContent);

	loadView();

	$.baserAjaxDataList.config.methods.del.confirm = 'コンテンツをゴミ箱に移動してもよろしいですか？';
	$.baserAjaxBatch.config.methods.del.confirm = '選択したデータを全てゴミ箱に移動します。よろしいですか？\n※ エイリアスは直接削除します。';
	$.baserAjaxDataList.config.methods.publish.result = null;
	$.baserAjaxDataList.config.methods.unpublish.result = null;
	$.baserAjaxDataList.config.methods.copy.result = null;
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $.baseUrl + '/admin/contents/ajax_batch'});
	
	$("#ViewSetting").after($("#Search"));
	
/**
 * 表示初期化
 */
	function loadView(e) {

		// サイトが変わった場合はリセット
		if(e !== undefined && e.target.id == 'ViewSettingSiteId') {
			$("#BtnSearchClear").click();
			$.ajax({
				url: $.baseUrl + '/admin/contents/ajax_get_content_folder_list/' + $(this).val(),
				type: "GET",
				dataType: "json",
				beforeSend: function(){
					$("#ContentFolderId").attr('disabled', "disabled");
				},
				complete: function(){
					$("#ContentFolderId").removeAttr("disabled");
				},
				success: function(result){
					$("#ContentFolderId").empty();
					var optionItems = [];
					optionItems.push(new Option("指定なし", ""));
					for (key in result) {
						optionItems.push(new Option(result[key].replace(/&nbsp;/g,"\u00a0"), key));
					}
					$("#ContentFolderId").append(optionItems);
				}
			});
		}
		var mode = $("#ViewSettingMode").val();
		var listType = $("input[name='data[ViewSetting][list_type]']:checked").val();
		if(listType == undefined || mode == 'trash') {
			listType = "1";
		}
		switch(listType) {
			case "1":
				$.bcTree.load();
				$("#BtnMenuSearch").hide();
				$("#BtnAddContent").parent().show();
				if($("#Search").is(":hidden")) {
					contentsIndexSearchOpened = false;
				} else {
					contentsIndexSearchOpened = true;
				}
				$("#Search").hide();
				break;
			case "2":
				loadTable();
				$("#BtnMenuSearch").show();
				$("#BtnAddContent").parent().hide();
				if(contentsIndexSearchOpened) {
					$("#Search").show();
				} else {
					$("#Search").hide();
				}
				break;
		}

	}

/**
 * 表形式のリストをロードする 
 */
	function loadTable() {
		url = $.baseUrl + '/admin/contents/index/site_id:' + $("#ViewSettingSiteId").val() + '/list_type:2';
		$("#ContentIndexForm").attr('action', url);
		$.baserAjaxDataList.search();
	}
	
});