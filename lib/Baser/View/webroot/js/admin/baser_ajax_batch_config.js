/* SVN FILE: $Id$ */
/**
 * baserAjaxBatch 設定
 * 
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$.extend($.baserAjaxBatch.config, {
	url			: '',
	listTable	: '#ListTable',
	pageTotalNum: '.page-total-num',
	pageStartNum: '.page-start-num',
	pageEndNum	: '.page-end-num',
	dataList	: '#DataList',
	methods		: {
	/**
	 * 削除
	 */
		del: {
			confirm: '選択したデータを全て削除します。よろしいですか？\n※ 削除したデータは元に戻すことができません。', 
			result: function() {
				var config = $.baserAjaxBatch.config;
				var colspan = $(config.targetCheckbox+":checked:first").parent().parent().find('td').length;
				var delNum = $(config.targetCheckbox+":checked").length;
				$(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) - delNum);
				$(config.pageEndNum).html(Number($(config.pageEndNum).html()) - delNum);
				$(config.targetCheckbox+":checked").parent().parent().fadeOut(300, function(){
					$(this).remove();
					if($(config.listTable+" tbody td").length) {
						$.baserAjaxDataList.initList();
						$(config.listTable+" tbody tr").removeClass('even odd');
						$.yuga.stripe();
					} else {
						$.baserAjaxDataList.load(document.location.href);
						$(config.listTable+" tbody").append('<td colspan="'+colspan+'"><p class="no-data">データがありません。</p></td>');
					}
				});
			}
		},
	/**
	 * 公開処理
	 */
		publish: {
			confirm: '選択したデータを全て公開状態に変更します。よろしいですか？',
			result: function() {
				var config = $.baserAjaxBatch.config;
				var row = $(config.targetCheckbox+":checked").parent().parent();
				row.removeClass('publish');
				row.removeClass('unpublish');
				row.removeClass('disablerow');
				row.addClass('publish');
				row.find('.status').html('○');
				$(config.targetCheckbox+":checked").removeAttr('checked');
				$.baserAjaxDataList.initList();
			}
		},
	/**
	 * 非公開処理
	 */
		unpublish: {
			confirm: '選択したデータを全て非公開状態に変更します。よろしいですか？',
			result: function() {
				var config = $.baserAjaxBatch.config;
				var row = $(config.targetCheckbox+":checked").parent().parent();
				row.removeClass('publish');
				row.removeClass('unpublish');
				row.addClass('disablerow');
				row.addClass('unpublish');
				row.find('.status').html('―');
				$(config.targetCheckbox+":checked").removeAttr('checked');
				$.baserAjaxDataList.initList();
			}
		}
	}
});