$(function(){
	$("body").append($("#FavoritesMenu"));
	$("#BtnFavoriteAdd").click(function(){
		$('#FavoriteDialog').dialog('open');
		return false;
	});
/**
 * お気に入り初期化
 */
	initFavoriteList();
/**
 * バリデーション
 */
	$("#FavoriteAjaxForm").validate();
	$("#FavoriteAjaxForm").submit(function(){return false});
/**
 * ダイアログを初期化
 */
	$("#FavoriteDialog").dialog({
		bgiframe: true,
		autoOpen: false,
		position: [250, 150],
		width: '360px',
		modal: true,
		open: function(event, ui){

			if($(".favorite-menu-list .selected").size() == 0) {
				$(this).dialog('option', 'title', 'よく使う項目登録');
				$("#FavoriteName").val($("#CurrentPageName").html());
				$("#FavoriteUrl").val($("#CurrentPageUrl").html());
			} else {
				$(this).dialog('option', 'title', 'よく使う項目編集');
				$("#FavoriteId").val($(".favorite-menu-list .selected .favorite-id").val());
				$("#FavoriteName").val($(".favorite-menu-list .selected .favorite-name").val());
				$("#FavoriteUrl").val($(".favorite-menu-list .selected .favorite-url").val());
			}
			$("#FavoriteAjaxForm").submit();
			$("#FavoriteName").focus();
			
		},
		close: function() {
			$("#FavoriteId").val('');
			$("#FavoriteName").val('');
			$("#FavoriteUrl").val('');
		},
		buttons: {
			'キャンセル': function() {
				$(this).dialog('close');
			},
			'保存': function() {
				var submitUrl = $("#FavoriteAjaxForm").attr('action');
				if(!$("#FavoriteId").val()) {
					submitUrl += '_add';
				} else {
					submitUrl += '_edit/'+$("#FavoriteId").val();
				}
				var favoriteId = $("#FavoriteId").val();
				if($("#FavoriteAjaxForm").valid()) {
					$.bcToken.check(function(){
						$('#FavoriteAjaxForm input[name="data[_Token][key]"]').val($.bcToken.key);
						$("#FavoriteAjaxForm").ajaxSubmit({
							url: submitUrl,
							beforeSend: function() {
								$("#Waiting").show();
							},
							success: function(response, status) {
								if(response) {
									if($("#FavoriteId").val()) {
										var currentLi = $("#FavoriteId"+favoriteId).parent();
										currentLi.after(response);
										currentLi.remove();
									} else {
										var favoriteRowId = 1;
										if($(".favorite-menu-list li.no-data").length == 1) {
											$(".favorite-menu-list li.no-data").remove();
										}
										if($(".favorite-menu-list li").length) {
											favoriteRowId = Number($(".favorite-menu-list li:last").attr('id').replace('FavoriteRow', ''))+1;
										}
										$(".favorite-menu-list li:last").attr('id', 'FavoriteRow'+favoriteRowId);
										$(".favorite-menu-list").append(response);
									}
									initFavoriteList();
									$("#FavoriteDialog").dialog('close');
								} else {
									alert('保存に失敗しました。');
								}
							},
							error: function(XMLHttpRequest, textStatus){
								if(XMLHttpRequest.responseText) {
									alert('よく使う項目の追加に失敗しました。\n\n' + XMLHttpRequest.responseText);
								} else {
									alert('よく使う項目の追加に失敗しました。\n\n' + XMLHttpRequest.statusText);
								}
							},
							complete: function(){
								$("#Waiting").hide();
							}
						});
					}, {hideLoader: false});
				}
			}
		}

	});
	
/**
 * 並び替え開始時イベント
 */
	function favoriteSortStartHandler(event, ui) {	
		$("ul.favorite-menu-list .placeholder").css('height',ui.item.height());
	}
	
/**
 * 並び順を更新時イベント
 */
	function favoriteSortUpdateHandler(event, ui){

		var target = ui.item;
		var targetNum = $(".favorite-menu-list li").index(target) + 1;
		var sourceNum = target.attr('id').replace('FavoriteRow','');
		var offset = targetNum - sourceNum;
		var sortTable = $(".favorite-menu-list");
		var form = $('<form/>').hide();
		var sortId = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][id]').val(target.find('.favorite-id').val());
		var sortOffset = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][offset]').val(offset);
		form.append(sortId).append(sortOffset);

		$.bcToken.check(function(){
			form.append($.bcToken.getHiddenToken());
			$.ajax({
				url: $("#FavoriteAjaxSorttableUrl").html(),
				type: 'POST',
				data: form.serialize(),
				dataType: 'text',
				beforeSend: function() {
					$("#Waiting").show();
				},
				success: function(result){
					if(result == '1') {
						var i = 0;
						sortTable.find(".favorite-menu-list li").each(function(){
							$(this).attr('id','FavoriteRow'+(i));
							i++;
						});
					} else {
						sortTable.sortable("cancel");
						alert('並び替えの保存に失敗しました。');
					}
				},
				error: function(){
					sortTable.sortable("cancel");
					alert('並び替えの保存に失敗しました。');
				},
				complete: function() {
					$("#Waiting").hide();
				}
			});
		}, {hideLoader: false});
	}
	
/**
 * 行を初期化
 */
	function initFavoriteList() {

		// イベント削除
		$(".favorite-menu-list li").unbind();
		$(".favorite-menu-list li").destroyContextMenu();
		$(".favorite-menu-list").sortable("destroy");

		// イベント登録
		var favoriteSortableOptions = {
			scroll: true,
			opacity: 0.80,
			zIndex: 55,
			containment: 'body',
			tolerance: 'pointer',
			distance: 5,
			cursor: 'pointer',
			placeholder: 'ui-widget-content placeholder',
			/*handle: ".favorite-menu-list li a",*/
			revert: 100,
			start: favoriteSortStartHandler,
			update: favoriteSortUpdateHandler
		};
		$(".favorite-menu-list").sortable(favoriteSortableOptions);
		$(".favorite-menu-list li").contextMenu({menu: 'FavoritesMenu'}, contextMenuClickHandler);

		// IEの場合contextmenuを検出できなかったので、mousedownに変更した
		$(".favorite-menu-list li").bind('mousedown', function(){
			$(".favorite-menu-list li").removeClass('selected');
			$(this).addClass('selected');
			$(".favorite-menu-list li").unbind('outerClick.selected');
			$(this).bind('outerClick.selected', function() {
				$(".favorite-menu-list li").removeClass('selected');
			});
		});

		var i = 1;
		$(".favorite-menu-list li").each(function(){
			// アクセス制限によってリンクが出力されていない場合はLIごと削除する
			if($(this).find('a').html() == null) {
				$(this).remove();
			} else {
				$(this).attr('id', 'FavoriteRow'+(i));
				i++;
			}
		});

	}
	
/**
 * コンテキストメニュークリックハンドラ
 */
	function contextMenuClickHandler(action, el, pos) {

		// IEの場合、action値が正常に取得できないので整形する
		var pos = action.indexOf("#");

		if(pos != -1){
			action = action.substring(pos+1,action.length);
		}

		switch (action){

			case 'FavoriteEdit':
				$('#FavoriteDialog').dialog('open');
				break;

			case 'FavoriteDelete':
				var id = $(".favorite-menu-list .selected .favorite-id").val();
				if(confirm('本当に削除してもよろしいですか？')){
					$.bcToken.check(function(){
						var data = {
							data: {
								Favorite: {id: id},
								_Token: {key: $.bcToken.key}
						}};
						$.ajax({
							url: $("#FavoriteDeleteUrl").html(),
							type: 'POST',
							data: data,
							dataType: 'text',
							beforeSend: function() {
								$("#Waiting").show();
							},
							success: function(result){
								if(result){
									$(".favorite-menu-list .selected").fadeOut(300, function(){
										$(this).remove();
									});
								} else {
									alert("サーバーでの処理に失敗しました。");
								}
								$("#Waiting").hide();
							},
							error: function(){
								alert("サーバーでの処理に失敗しました。");
							},
							complete: function() {
								$("#Waiting").hide();
							}
						});
					}, {hideLoader: false});
				}
				break;
		}
	}
	
});
