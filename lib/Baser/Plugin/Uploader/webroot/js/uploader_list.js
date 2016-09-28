/* SVN FILE: $Id$ */
/**
 * アップロードリスト
 *
 * PHP versions 5
 *
 * Baser :  Basic Creating Support Project <http://basercms.net>
 * Copyright 2008 - 2013, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2013, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * 起動時処理
 */
$(function(){

	var listId = $("#ListId").html();
	
	//==========================================================================
	// 複数のエディタよりリストが呼出される可能性がある為、#ListIdの値を読み込んだら削除する
	// TODO 強引すぎるので他の方法がないか検討要
	//==========================================================================
	$("#ListId").remove();
	
	var allFields = $([]).add($("#name")).add($("#alt"));
	var baseUrl = $.baseUrl + '/';
	var adminPrefix = $("#AdminPrefix").html();
	var categoryId = null;
	
	// 右クリックメニューをbodyに移動
	$("body").append($("#FileMenu1"));
	$("body").append($("#FileMenu2"));

	// 一覧を更新する
	updateFileList();

	/* ダイアログを初期化 */
	$("#EditDialog").dialog({
		bgiframe: true,
		autoOpen: false,
		position: ['center', 20],
		width:640,
		modal: true,
		open: function(){
			var name = $("#FileList"+listId+" .selected .name").html();
			var imgUrl = baseUrl + adminPrefix + '/uploader/uploader_files/ajax_image/'+name+'/midium';
			$("#UploaderFileId"+listId).val($("#FileList" + listId + " .selected .id").html());
			$("#UploaderFileName"+listId).val(name);
			$("#UploaderFileAlt"+listId).val($("#FileList"+listId+" .selected .alt").html());

			/* ダイアログ初期化時、hidden値が空になるため公開期間開始日時を取得して hidden値に入れ込む */
			var publishBeginDate = $("#FileList"+listId+" .selected .publish-begin").html();
			var publishBeginTime = $("#FileList"+listId+" .selected .publish-begin-time").html();
			$("#UploaderFilePublishBeginDate").val(publishBeginDate);
			$("#UploaderFilePublishBeginTime").val(publishBeginTime);
			var publishBeginDateTime = publishBeginDate + ' ' + publishBeginTime;
			$("#UploaderFilePublishBegin").val(publishBeginDateTime);

			/* ダイアログ初期化時、hidden値が空になるため公開期間終了日時を取得して hidden値に入れ込む */
			var publishEndDate = $("#FileList"+listId+" .selected .publish-end").html();
			var publishEndTime = $("#FileList"+listId+" .selected .publish-end-time").html();
			$("#UploaderFilePublishEndDate").val(publishEndDate);
			$("#UploaderFilePublishEndTime").val(publishEndTime);
			var publishEndDateTime = publishEndDate + ' ' + publishEndTime;
			$("#UploaderFilePublishEnd").val(publishEndDateTime);

			$("#UploaderFileUserId"+listId).val($("#FileList"+listId+" .selected .user-id").html());
			$("#UploaderFileUserName"+listId).html($("#FileList"+listId+" .selected .user-name").html());
			if($("#_UploaderFileUploaderCategoryId"+listId).length) {
				$("#_UploaderFileUploaderCategoryId"+listId).val($("#FileList"+listId+" .selected .uploader-category-id").html());
			}
			$.get(imgUrl,function(res){
				$("#UploaderFileImage"+listId).html(res);
			});
		},
		buttons: {
			'キャンセル': function() {
				$(this).dialog('close');
				$("#UploaderFileImage"+listId).html('<img src="'+baseUrl+'img/admin/ajax-loader.gif" />');
			},
			'保存': function() {
				// 保存処理
				var saveButton = $(this);
				$.bcToken.check(function(){
					// IEでform.serializeを利用した場合、Formタグの中にTableタグがあるとデータが取得できなかった
					var data = {"data[UploaderFile][id]":$("#UploaderFileId"+listId).val(),
						"data[UploaderFile][name]":$("#UploaderFileName"+listId).val(),
						"data[UploaderFile][alt]":$("#UploaderFileAlt"+listId).val(),
						"data[UploaderFile][publish_begin]":$("#UploaderFilePublishBegin"+listId).val(),
						"data[UploaderFile][publish_end]":$("#UploaderFilePublishEnd"+listId).val(),
						"data[UploaderFile][user_id]":$("#UploaderFileUserId"+listId).val(),
						"data[UploaderFile][uploader_category_id]":$("#_UploaderFileUploaderCategoryId"+listId).val(),
						"data[_Token][key]": $.bcToken.key
					};
					$.post($("#UploaderFileEditForm"+listId).attr('action'), data, function(res){
						if (res) {
							updateFileList();
							allFields.removeClass('ui-state-error');
							saveButton.dialog('close');
							$("#UploaderFileImage"+listId).html('<img src="'+baseUrl+'img/admin/ajax-loader.gif" />');
						} else {
							alert('更新に失敗しました');
						}
					});
				}, {hideLoader: false});
			}
		},
		close: function() {
			allFields.val('').removeClass('ui-state-error');
			$("#UploaderFileImage"+listId).html('<img src="'+baseUrl+'img/admin/ajax-loader.gif" />');
		}

	});
/**
 * アップロードファイル選択時イベント
 */
	function uploaderFileFileChangeHandler(){

		var url = baseUrl + adminPrefix + '/uploader/uploader_files/ajax_upload';
		var form = $(this);
		$("#Waiting").show();

		if($('#UploaderFileFile'+listId).val()){
			$.bcToken.check(function(){
				var data = {'data[_Token][key]': $.bcToken.key};
				if($("#UploaderFileUploaderCategoryId"+listId).length) {
					data = $.extend(data, {'data[UploaderFile][uploader_category_id]':$("#UploaderFileUploaderCategoryId"+listId).val()});
				}
				form.upload(url, data, uploadSuccessHandler, 'html');
			}, {hideLoader: false});

		}

	}
/**
 * アップロード完了後イベント
 */
	function uploadSuccessHandler(res){
		
		if(res){
			if($('#UploaderFileUploaderCategoryId'+listId).length) {
				$('#FilterUploaderCategoryId'+listId).val($('#UploaderFileUploaderCategoryId'+listId).val());
				categoryId =$('#UploaderFileUploaderCategoryId'+listId).val();
			}
			updateFileList();
		}else{
			$('#ErrorMessage').remove();
			$('#FileList'+listId).prepend('<p id="ErrorMessage" class="message">アップロードに失敗しました。ファイルサイズを確認してください。</p>');
			$("#Waiting").hide();
		}
		// フォームを初期化
		// セキュリティ上の関係でvalue値を直接消去する事はできないので、一旦エレメントごと削除し、
		// spanタグ内に新しく作りなおす。
		$("#UploaderFileFile"+listId).remove();
		$("#SpanUploadFile"+listId).append('<input id="UploaderFileFile'+listId+'" type="file" value="" name="data[UploaderFile][file]" class="uploader-file-file" />');
		$('#UploaderFileFile'+listId).change(uploaderFileFileChangeHandler);

	}
/**
 * 一覧を更新する
 */
	function updateFileList(){

		$("#Waiting").show();
		$.get(getListUrl(),updateFileListCompleteHander);

	}
/**
 * 選択イベントを初期化する
 */
	function initFileList(){

		var usePermission = $("#UsePermission").html();

		if(categoryId) {
			$('#UploaderFileUploaderCategoryId'+listId).val(categoryId);
		}
		/* 一旦イベントを全て解除 */
		$(".selectable-file").unbind('click.selectEvent');
		$(".selectable-file").unbind('mouseenter.selectEvent');
		$(".selectable-file").unbind('mouseleave.selectEvent');
		$(".page-numbers a").unbind('click.paginationEvent');
		$(".selectable-file").unbind('dblclick.dblclickEvent');
		$(".filter-control").unbind('click.filterEvent');
		$(".btn-delete").unbind('click');

		/* 公開制限期間にあるファイルの背景色を定義 */
		var unpublishBackGroundColor = '#bbb';
		$("#DivPanelList .selectable-file").each(function(){

			if($(this).contextMenu && !listId) {
				/* 右クリックメニューを追加 */
				if($(this).find('.user-id').html() == $("#LoginUserId").html() || $("#LoginUserGroupId").html() == 1 || !Number(usePermission)) {
					$(this).contextMenu({menu: 'FileMenu1'}, contextMenuHander);
					$(this).bind('dblclick.dblclickEvent', function(){
						$('#EditDialog').dialog('open');
					});
				} else {
					$(this).contextMenu({menu: 'FileMenu2'}, contextMenuHander);
					$(this).bind('dblclick.dblclickEvent', function(){
						alert('このファイルの編集・削除はできません。');
					});
				}
			} else {
				$(this).bind("contextmenu",function(e){
					return false;
				});
			}

			/* 公開制限期間にあるファイルは背景色をグレーにする */
			if ($(this).hasClass('unpublish')) {
				$(this).css('background-color', unpublishBackGroundColor);
			}
		});

		// ファイルアップロードイベントを登録
		$('#UploaderFileFile'+listId).change(uploaderFileFileChangeHandler);

		if(listId) {
			$(".selectable-file").bind('mouseenter.selectEvent', function(){
				$(this).css('background-color','#FFCC00');
			});
			$(".selectable-file").bind('mouseleave.selectEvent', function(){
				$(this).css('background-color','#FFFFFF');
				if ($(this).hasClass('unpublish')) {
					$(this).css('background-color', unpublishBackGroundColor);
				}
			});
			$(".selectable-file").each(function(){
				// IEの場合contextmenuを検出できなかったので、mousedownに変更した
				$(this).bind('mousedown', function(){
					$(".selectable-file").removeClass('selected');
					$(this).addClass('selected');
				});
			});
		} else {
			$("#DivPanelList .selectable-file").bind('mouseenter.selectEvent', function(){
				$(this).css('background-color','#FFCC00');
			});
			$("#DivPanelList .selectable-file").bind('mouseleave.selectEvent', function(){
				$(this).css('background-color','#FFFFFF');
				if ($(this).hasClass('unpublish')) {
					$(this).css('background-color', unpublishBackGroundColor);
				}
			});	
			$("#DivPanelList .selectable-file").each(function(){
				// IEの場合contextmenuを検出できなかったので、mousedownに変更した
				$(this).bind('mousedown', function(){
					$(".selectable-file").removeClass('selected');
					$(this).addClass('selected');
				});
			});
		}

		/* ページネーションイベントを追加 */
		$('.page-numbers a').bind('click.paginationEvent', function(){
			$("#Waiting").show();
			$.get($(this).attr('href'),updateFileListCompleteHander);
			return false;
		});

		$("#BtnFilter"+listId).bind('click.filterEvent', function(){
			$("#Waiting").show();
			$.get(getListUrl(),updateFileListCompleteHander);			
		});
		/*$('#FilterUploaderCategoryId'+listId).bind('change.filterEvent', function() {
			$("#Waiting").show();
			$.get(getListUrl(),updateFileListCompleteHander);
		});
		$('input[name="data[Filter][uploader_type]"]').bind('click.filterEvent', function() {
			$("#Waiting").show();
			$.get(getListUrl(),updateFileListCompleteHander);
		});*/

		$('.selectable-file').corner("5px");
		$('.corner5').corner("5px");
		$("#FileList"+listId).trigger("filelistload");
		$("#FileList"+listId).effect("highlight",{},1500);

	}
/**
 * ファイルリスト取得完了イベント
 */
	function updateFileListCompleteHander(result) {

		$("#FileList"+listId).html(result);
		initFileList();
		$("#Waiting").hide();

	}
/**
 * Ajax List 取得用のURLを取得する
 */
	function getListUrl() {

		var listUrl = $("#ListUrl"+listId).attr('href');
		if($('#FilterUploaderCategoryId'+listId).length) {
			listUrl += '/uploader_category_id:'+$('#FilterUploaderCategoryId'+listId).val();
		}
		if($('input[name="data[Filter][uploader_type]"]:checked').length) {
			listUrl += '/uploader_type:'+$('input[name="data[Filter][uploader_type]"]:checked').val();
		}
		if($('#FilterName'+listId).val()) {
			listUrl += '/name:'+ encodeURI($('#FilterName'+listId).val());
		}
		return listUrl;

	}
/**
 * コンテキストメニューハンドラ
 */
	function contextMenuHander(action, el, pos) {

		var delUrl = baseUrl + adminPrefix + '/uploader/uploader_files/delete/' + $("#FileList"+listId+" .selected .id").html();

		// IEの場合、action値が正常に取得できないので整形する
		var pos = action.indexOf("#");

		if(pos != -1){
			action = action.substring(pos+1,action.length);
		}

		switch (action){

			case 'edit':
				$('#EditDialog').dialog('open');
				break;

			case 'delete':
				if(confirm('本当に削除してもよろしいですか？')){
					$.bcToken.check(function(){
						$("#Waiting").show();
						$.post(delUrl, {
							_Token: {key: $.bcToken.key}
						}, function(res){
							if(!res){
								$("#Waiting").hide();
								alert("サーバーでの処理に失敗しました。");
							}else{
								$("#FileList"+listId).trigger("deletecomplete");
								updateFileList();
							}
						});
					}, {hideLoader: false});
				}
				break;
		}

	}


});
