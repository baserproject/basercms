/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


$(function() {

	var delWidgetUrl = $("#AdminWidgetFormScript").attr('data-delWidgetUrl');
	var currentAction = $("#AdminWidgetFormScript").attr('data-currentAction');
	var sortableOptions = {
		scroll: true,
		items: 'div.sortable',
		opacity: 0.80,
		zIndex: 55,
		containment: 'body',
		tolerance: 'intersect',
		distance: 5,
		cursor: 'move',
		placeholder: 'ui-widget-content placeholder',
		deactivate: function(event,ui){
			// 新しいウィジェットを追加しようとしてやめた場合
			// 再度追加する際に原因不明のエラーが連続で出現してしまうので、
			// 一旦リセットするようにした。
			$("#Target").sortable("destroy");
			$("#Target").sortable(sortableOptions
			).droppable(
				{
					hoverClass: 'topDrop',
					accept: 'div.sortable',
					tolderance: 'intersect'
				});
		},
		update: function(event, ui){
			// jQueryUI 1.8.14 より、 ui.item.attr("id")で id が取得できない
			if($(ui.item.context).attr("id").match(/^Setting/i)){
				widgetAreaUpdateSortedIds();
				return;
			}

			var baseId = 0;
			$("#Target .setting").each(function () {
				var _baseId = parseInt($(this).attr('id').replace('Setting',''));
				if(_baseId > baseId){
					baseId = _baseId;
				}
			});

			baseId++;
			var id = $(ui.item.context).attr("id").replace('Widget','');
			var sourceId = id.replace('Widget','');
			var settingId = 'Setting' + (baseId);
			var tmpId = 'Tmp'+(baseId);

			/* ターゲットにテンプレートを追加 */
			ui.item.attr('id',tmpId);
			$("#"+tmpId).after($("#"+sourceId).clone().attr('id',settingId));
			$("#"+tmpId).remove();
			$("#"+settingId).addClass('setting');
			$("#"+settingId).removeClass('template');

			/* フィールドIDのリネーム */
			renameWidget(baseId);

			/* 値の設定 */
			var widgetname = $("#"+settingId+' .widget-name').html();
			$("#"+settingId+' .head').html($("#"+settingId+' .head').html()+$("#Target ."+widgetname).length);
			$("#WidgetId"+baseId).val(baseId);
			$("#WidgetName"+baseId).val($("#"+settingId+' .head').html());

			/* イベント登録 */
			registWidgetEvent(baseId);

			/* sortable をリフレッシュ */
			$("#Target").sortable("refresh");

			$("#"+settingId+" .content").slideDown('fast');

			/* ウィジェットを保存 */
			updateWidget(baseId);

		},
		activate: function(event, ui) {
			// ドラッグ時の幅を元の幅に合わせる
			$("#Source div:last").width(ui.item.width()-20);
		}
	};
	$("#Target").sortable(sortableOptions).droppable(
		{
			hoverClass: 'topDrop',
			accept: 'div.draggable',
			tolderance: 'intersect'
		});
	$("div.draggable").draggable(
		{
			scroll: true,
			helper: 'clone',
			opacity: 0.80,
			revert: 'invalid',
			cursor: 'move',
			connectToSortable: '#Target',
			containment: 'body'
		});

	$("#Target .sortable").each(function(k,v){
		registWidgetEvent($(this).attr('id').replace('Setting',''));
	});

	if(currentAction == 'admin_edit') {
		$("#WidgetAreaUpdateTitleSubmit").click(function(){
			widgetAreaUpdateTitle();
			return false;
		});
	}
	
/**
 * ウィジェットごとにid/nameをリネームする
 */
	function renameWidget(baseId){

		var settingId = 'Setting'+baseId;
		$("#"+settingId+' .form').attr('id','WidgetUpdateWidgetForm'+baseId);
		$("#WidgetUpdateWidgetForm"+baseId).find('input, select, textarea').each(function(){
			if($(this).attr('id')){
				$(this).attr('id',$(this).attr('id')+baseId);
			}
			if($(this).attr('name')!=undefined){
				if($(this).attr('name').match(/data\[Widget\]/i)){
					$(this).attr('name',$(this).attr('name').replace('data[Widget]','data[Widget'+baseId+']'));
				}
			}
		});
		$("#"+settingId+" label[for=WidgetStatus]").attr('for','WidgetStatus'+baseId);

	}
	
/**
 * ウィジェットイベントを登録
 */
	function registWidgetEvent(baseId){

		var settingId = 'Setting'+baseId;
		$("#WidgetUpdateWidgetSubmit"+baseId).click(function (){
			updateWidget(baseId);
			return false;
		});
		$("#"+settingId+" .action").click(function(){
			if($("#"+settingId+" .content").is(":hidden")){
				$("#"+settingId+" .content").slideDown('fast');
			}else{
				$("#"+settingId+" .content").slideUp('fast');
			}
		});
		$("#"+settingId+" .status").click(function(){
			if($("#"+settingId+" .status").attr('checked')){
				$("#"+settingId).addClass('enabled');
			}else{
				$("#"+settingId).removeClass('enabled');
			}
		});
		$("#"+settingId+" .del").click(function(){
			if(!confirm('設定内容も削除されますが、本当に削除してもいいですか？\n')){
				return;
			}
			delWidget(baseId);
		});

	}
	
/**
 * ウィジェットを削除
 */
	function delWidget(id){

		$.ajax({
			url: delWidgetUrl + '/' + id,
			type: 'GET',
			dataType: 'text',
			beforeSend: function() {
				$("#WidgetAreaUpdateSortLoader").show();
				$("#flashMessage").slideUp();
			},
			success: function(result){
				if(result != '1'){
					$("#flashMessage").html('ウィジェットの削除に失敗しました。');
					$("#flashMessage").slideDown();
				} else {
					$("#Setting"+id+"").slideUp(200, function(){
						$("#Setting"+id).remove();
						widgetAreaUpdateSortedIds();
					});
				}
			},
			error: function(){
				$("#flashMessage").html('ウィジェットの削除に失敗しました。');
				$("#flashMessage").slideDown();
			},
			complete: function(xhr, textStatus) {
				$("#WidgetAreaUpdateSortLoader").hide();
			}

		});

	}
	
/**
 * 並び順を更新する
 */
	function widgetAreaUpdateSortedIds(){

		var ids = [];
		$("#Target .sortable").each(function(k,v){
			ids.push($(this).attr('id').replace('Setting',''));
		});
		$("#WidgetAreaSortedIds").val(ids.join(','));
		$.ajax({
			url: $("#WidgetAreaUpdateSortForm").attr('action'),
			type: 'POST',
			data: $("#WidgetAreaUpdateSortForm").serialize(),
			dataType: 'text',
			beforeSend: function() {
				$("#flashMessage").slideUp();
				$("#WidgetAreaUpdateSortLoader").show();
			},
			success: function(result){
				if(result != '1'){
					$("#flashMessage").html('ウィジェットエリアの並び替えの保存に失敗しました。');
					$("#flashMessage").slideDown();
				}
			},
			error: function(){
				$("#flashMessage").html('ウィジェットエリアの並び替えの保存に失敗しました。');
				$("#flashMessage").slideDown();
			},
			complete: function(xhr, textStatus) {
				$("#WidgetAreaUpdateSortLoader").hide();
			}
		});

	}
	
/**
 * タイトルを更新する
 */
	function widgetAreaUpdateTitle(){

		$.ajax({
			url: $("#WidgetAreaUpdateTitleForm").attr('action'),
			type: 'POST',
			data: $("#WidgetAreaUpdateTitleForm").serialize(),
			dataType: 'text',
			beforeSend: function() {
				$("#WidgetAreaUpdateTitleSubmit").attr('disabled', 'disabled');
				$("#flashMessage").slideUp();
				$("#WidgetAreaUpdateTitleLoader").show();
			},
			success: function(result){
				if(result){
					$("#flashMessage").html('ウィジェットエリア名を保存しました。');
				}else{
					$("#flashMessage").html('ウィジェットエリア名の保存に失敗しました。');
				}
				$("#flashMessage").slideDown();
			},
			error: function(){
				$("#flashMessage").html('ウィジェットエリア名の保存に失敗しました。');
				$("#flashMessage").slideDown();
			},
			complete: function(xhr, textStatus) {
				$("#WidgetAreaUpdateTitleSubmit").removeAttr('disabled');
				$("#WidgetAreaUpdateTitleLoader").hide();
			}
		});

	}
	
/**
 * ウィジェットを更新する
 */
	function updateWidget(id) {

		$.ajax({
			url: $("#WidgetUpdateWidgetForm"+id).attr('action'),
			type: 'POST',
			data: $("#WidgetUpdateWidgetForm"+id).serialize(),
			dataType: 'text',
			beforeSend: function() {
				$("#WidgetUpdateWidgetSubmit"+id).attr('disabled', 'disabled');
				$("#WidgetUpdateWidgetLoader"+id).show();
				$("#flashMessage").slideUp();
			},
			success: function(result){
				if(result != '1'){
					$("#flashMessage").html('ウィジェットの保存に失敗しました。');
					$("#flashMessage").slideDown();
				}else{
					$("#Setting"+id+' .head').html($("#Setting"+id+' .name').val());
				}
			},
			error: function(){
				$("#flashMessage").html('ウィジェットの保存に失敗しました。');
				$("#flashMessage").slideDown();
			},
			complete: function(xhr, textStatus) {
				$("#WidgetUpdateWidgetSubmit"+id).removeAttr('disabled');
				$("#WidgetUpdateWidgetLoader"+id).hide();
				widgetAreaUpdateSortedIds();
			}

		});

	}
});
