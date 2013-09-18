/* SVN FILE: $Id$ */
/**
 * baserAjaxSortTable プラグイン
 * 
 * 並び替え可能なテーブルを実装する
 * 
 * 【必要ライブラリ】
 * jquery / jquery-ui / yuga
 * 
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
(function($){

    /* 関数にオプション変数を渡す */
    $.baserAjaxSortTable = {
		config: {
			listTable	: ".sort-table",
			handle		: ".sort-handle",
			items		: "tr.sortable",
			placeHolder	: ".placeholder",
			alertBox	: "#AlertMessage",
			loader		: '#Waiting',
			flashBox	: '#flashMessage'
		},
		init: function(config){
			if(config) {
				$.extend($.baserAjaxSortTable.config, config);
			}
			config = $.baserAjaxSortTable.config;

		},
		initList: function() {
			
			var config = $.baserAjaxSortTable.config;
			
			// イベント削除
			$(config.handle).unbind();
			$(config.listTable).sortable("destroy");
			
			// イベント登録
			var sortableOptions = {
				scroll: true,
				items: config.items,
				opacity: 0.80,
				zIndex: 55,
				containment: 'body',
				tolerance: 'pointer',
				distance: 5,
				cursor: 'move',
				placeholder: 'ui-widget-content placeholder',
				handle: config.handle,
				revert: 100,
				start: sortStartHandler,
				stop: sortStopHander,
				update: sortUpdateHandler
			};
			$(config.handle).css('cursor', 'move');
			$(config.listTable).sortable(sortableOptions);
			$(config.handle).click(function(e){
				e.stopPropagation();
			});
			
		}
		
	};
/**
* 並び替え開始時イベント
*/
	function sortStartHandler(event, ui) {
		
		var config = $.baserAjaxSortTable.config;
		
		ui.item.css('border','2px solid #CCC');
		$(config.placeHolder).css('height',ui.item.height());
		for(var i = 0; i < ui.item.find('td').length; i++){
			$(config.placeHolder).append('<td>&nbsp;</td>');
		}
		$(config.placeHolder + " td").css('background-color', '#ffffbe');
		$(config.placeHolder + " td").css('border', 'none');
		
	}
/**
 * 並び替え終了時イベント
 */
	function sortStopHander(event, ui) {
		
		ui.item.css('border','none');
		
	}
/**
 * 並び順を更新時イベント
 */
	function sortUpdateHandler(event, ui){
		
		var config = $.baserAjaxSortTable.config;
		var target = ui.item;
		var targetNum = $(config.listTable + " " + config.items).index(target)+1;
		var sourceNum = target.attr('id').replace('Row','');
		var offset = targetNum - sourceNum;
		var sortTable = $(config.listTable);
		
		var form = $('<form/>').hide();
		var sortId = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][id]').val(target.find('.id').val());
		var sortOffset = $('<input/>').attr('type', 'hidden').attr('name', 'data[Sort][offset]').val(offset);
		form.append(sortId).append(sortOffset);

		$.ajax({
			url: config.url,
			type: 'POST',
			data: form.serialize(),
			dataType: 'text',
			beforeSend: function() {
				$(config.alertBox).fadeOut(200);
				$(config.flashBox).fadeOut(200);
				$(config.loader).show();
			},
			success: function(result){
				if(result == '1') {
					sortTable.find(config.items).each(function(i,v){
						$(this).attr('id','Row'+(i+1));
					});
				} else {
					sortTable.sortable("cancel");
					$(config.alertBox).html('並び替えの保存に失敗しました。');
					$(config.alertBox).fadeIn(500);
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
				sortTable.sortable("cancel");
				$(config.alertBox).html('並び替えの保存に失敗しました。('+XMLHttpRequest.status+')'+errorMessage);
				$(config.alertBox).fadeIn(500);
			},
			complete: function() {
				$(config.loader).hide();
				$(config.listTable + " " + config.items).removeClass('even odd');
				$.yuga.stripe();
			}
		});
		
	}
	
})(jQuery);