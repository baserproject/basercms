<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] よく使う項目
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<div id="FavoriteDeleteUrl" style="display: none"><?php $bcBaser->url(array('plugin' => null, 'controller' => 'favorites', 'action' => 'ajax_delete')) ?></div>
<div id="FavoriteAjaxSorttableUrl" style="display:none"><?php $bcBaser->url(array('plugin' => null, 'controller' => 'favorites', 'action' => 'update_sort')) ?></div>


<script type="text/javascript">
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
				$("#FavoriteAjaxForm").submit();
				if($("#FavoriteAjaxForm").valid()) {
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
						error: function() {
							alert('保存に失敗しました。');
						},
						complete: function(){
							$("#Waiting").hide();
						}
					});
				}
			}
		}

	});
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
		var targetNum = $(".favorite-menu-list li").index(target)+1;
		var sourceNum = target.attr('id').replace('FavoriteRow','');
		var offset = targetNum - sourceNum;
		var sortTable = $(".favorite-menu-list");
		var form = $('<form/>').hide();
		var sortId = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][id]').val(target.find('.favorite-id').val());
		var sortOffset = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][offset]').val(offset);
		form.append(sortId).append(sortOffset);

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
					sortTable.find(".favorite-menu-list li").each(function(i,v){
						$(this).attr('id','FavoriteRow'+(i+1));
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

		$(".favorite-menu-list li").each(function(i,v){
			// アクセス制限によってリンクが出力されていない場合はLIごと削除する
			if($(this).find('a').html() == null) {
				$(this).remove();
			} else {
				$(this).attr('id', 'FavoriteRow'+(i+1));
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

			case 'Edit':
				$('#FavoriteDialog').dialog('open');
				break;

			case 'Delete':
				if(confirm('本当に削除してもよろしいですか？')){
					$("#Waiting").show();
					$.post($("#FavoriteDeleteUrl").html(), {"data[Favorite][id]": $(".favorite-menu-list .selected .favorite-id").val()}, function(result){
						if(result){
							$(".favorite-menu-list .selected").fadeOut(300, function(){
								$(this).remove();
							});
						} else {
							alert("サーバーでの処理に失敗しました。");
						}
						$("#Waiting").hide();
					});
				}
				break;
		}
	}
</script>

<div id="FavoriteMenu">
		
	<h2><?php $bcBaser->img('admin/head_favorite.png', array('alt' => 'よく使う項目')) ?></h2>
	
	<ul class="favorite-menu-list">
<?php if($favorites): ?>
	
	<?php $count = 1 ?>
	<?php foreach($favorites as $favorite): ?>
		<?php $bcBaser->element('favorite_menu_row', array('favorite' => $favorite, 'count' => $count)) ?>
		<?php $count++ ?>
	<?php endforeach ?>
	
<?php else: ?>
		<li class="no-data">新規登録ボタンよりよく使う項目を登録しておく事ができます。</li>
<?php endif ?>
	</ul>
	
	<ul class="favolite-menu-tools clearfix">
		<li><?php $bcBaser->img('admin/btn_add.png', array('url' => 'javascript:void(0)', 'width' => 69, 'height' => 18, 'alt' => '新規追加', 'id' => 'BtnFavoriteAdd', 'class' => 'btn')) ?></li>
		<li><?php $bcBaser->img('admin/btn_menu_help.png', array('alt' => 'ヘルプ', 'width' => 60, 'height' => '18', 'class' => 'btn help', 'id' => 'BtnFavoriteHelp')) ?>
			<div class="helptext">
				<p>よく使う項目では、新規登録ボタンで現在開いているページへのリンクを簡単にする事ができます。<br />また、登録済の項目を右クリックする事で編集・削除が行えます。</p>
			</div>
		</li>
	</ul>

</div>

<div id="FavoriteDialog" title="よく使う項目" class="display-none">
	<?php echo $bcForm->create('Favorite', array('action' => 'ajax', 'url' => array('plugin' => null))) ?>
	<?php echo $bcForm->input('Favorite.id', array('type' => 'hidden')) ?>
	<dl>
		<dt><?php echo $bcForm->label('Favorite.name', 'タイトル') ?></dt><dd><?php echo $bcForm->input('Favorite.name', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
		<dt><?php echo $bcForm->label('Favorite.url', 'URL') ?></dt><dd><?php echo $bcForm->input('Favorite.url', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
	</dl>
	<?php echo $bcForm->end() ?>
</div>

	
<ul id="FavoritesMenu" class="context-menu" style="display:none">
    <li class="edit"><a href="#Edit">編集</a></li>
    <li class="delete"><a href="#Delete">削除</a></li>
</ul>