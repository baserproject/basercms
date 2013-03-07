<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリ一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$bcBaser->js(array(
	'admin/jquery.baser_ajax_data_list', 
	'admin/baser_ajax_data_list_config',
	'admin/jquery.baser_ajax_batch', 
	'admin/baser_ajax_batch_config'
), false);
?>

<script type="text/javascript">
$(function(){
	
	$.baserAjaxDataList.config.methods.copy = {
		button: '.btn-copy',
		confirm: '',
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				// =============================================================
				// Ajaxによる行の部分更新では、階層構造が取得できない為、一覧で取得しなおす
				// ============================================================= 
				$.baserAjaxDataList.load(document.location.href);
			} else {
				$(config.alertBox).html('コピーに失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		}
	}
	/**
	 * 上へ移動
	 */
	$.baserAjaxDataList.config.methods.up = {
		button: '.btn-up',
		confirm: '',
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				var rowClass = row.attr('class');
				var marches = rowClass.match(/depth-[0-9]+/);
				var depthClass = marches[0];
				var id = row.attr('id');
				var currentFlg = false;
				var sameRows = $('.'+depthClass);
				var targetRow = null;
				
				sameRows = sameRows.get().reverse();
				
				$(sameRows).each(function(){
					if(currentFlg) {
						targetRow = $(this);
						return false;
					}
					if(id == $(this).attr('id')) {
						currentFlg = true;
					}
				});
				
				var rowClassies = rowClass.split(' ');
				rowClassies = rowClassies.reverse();
				var rowGroupClass;
				$(rowClassies).each(function(){
					if(this.match(/row-group/)) {
						rowGroupClass = this;
						return false;
					}
				});
				
				$("."+rowGroupClass).hide();
				targetRow.before($("."+rowGroupClass));
				$("."+rowGroupClass).fadeIn(300);
				
				$.baserAjaxDataList.initList();
				row.fadeIn(300, function(){
					$(config.dataList+" tbody tr").removeClass('even odd');
					$.yuga.stripe();
				});
				
			} else {
				$(config.alertBox).html('移動に失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		},
		initList: function() {
			var config = $.baserAjaxDataList.config;
			if($(config.dataList + ' tbody tr ' + config.methods.up.button).get()=='') {
				return;
			}
			$(config.dataList + ' tbody tr ' + config.methods.up.button).show();
			$(config.dataList + ' tbody tr').each(function(){
				var depthGroup = [];
				var classies = parseClass($(this).attr('class'));
				var childDepth = Number(classies.depth) + 1;
				$(".depth-" + childDepth).each(function(){
				if($(this).attr('class').search('row-group-'+classies.current) != -1) {
					depthGroup.push($(this));
				}
				});
				if(depthGroup.length) {
				depthGroup[0].find(config.methods.up.button).hide();
				}
			});
			$(".depth-0:first").find(config.methods.up.button).hide();
			
		}
		
	};
/**
 * 下へ移動
 */
	$.baserAjaxDataList.config.methods.down = {
		button: '.btn-down',
		confirm: '',
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				
				var rowClass = row.attr('class');
				var marches = rowClass.match(/depth-[0-9]+/);
				var depthClass = marches[0];
				var id = row.attr('id');
				var currentFlg = false;
				var sameRows = $('.'+depthClass);
				var targetRow = null;
				
				sameRows.each(function(){
					if(currentFlg) {
						targetRow = $(this);
						return false;
					}
					if(id == $(this).attr('id')) {
						currentFlg = true;
					}
				});
				
				var targetRowClassies = targetRow.attr('class').split(' ')
				var targetRowGroupClass;
				$(targetRowClassies).each(function(){
					if(this.match(/row-group/)) {
						targetRowGroupClass = this;
						return false;
					}
				});
				
				var currentRowClassies = rowClass.split(' ');
				currentRowClassies = currentRowClassies.reverse();
				var currentRowGroupClass;
				$(currentRowClassies).each(function(){
					if(this.match(/row-group/)) {
						currentRowGroupClass = this;
						return false;
					}
				});
				$("."+currentRowGroupClass).hide();				
				$('.'+targetRowGroupClass+':last').after($("."+currentRowGroupClass));
				$("."+currentRowGroupClass).fadeIn(300);
				$.baserAjaxDataList.initList();
				row.fadeIn(300, function(){
					$(config.dataList+" tbody tr").removeClass('even odd');
					$.yuga.stripe();
				});

			} else {
				$(config.alertBox).html('移動に失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		},
		initList: function() {
			var config = $.baserAjaxDataList.config;
			if($(config.dataList + ' tbody tr ' + config.methods.down.button).get() == '') {
				return;
			}
			$(config.dataList + ' tbody tr ' + config.methods.down.button).show();
			$(config.dataList + ' tbody tr').each(function(){
				var depthGroup = [];
				var classies = parseClass($(this).attr('class'));
				var childDepth = Number(classies.depth) + 1;
				$(".depth-" + childDepth).each(function(){
					if($(this).attr('class').search('row-group-'+classies.current) != -1) {
						depthGroup.push($(this));
					}
				});
				if(depthGroup.length) {
					depthGroup[depthGroup.length-1].find(config.methods.down.button).hide();
				}
			});
			$(".depth-0:last").find(config.methods.down.button).hide();
		}
	};
	/**
	 * 削除
	 */
	$.baserAjaxDataList.config.methods.del = {
		button: '.btn-delete',
		confirm: 'このデータを本当に削除してもよろしいですか？\n※ このカテゴリに関連するページは、どのカテゴリにも関連しない状態として残ります。\n※ 削除したデータは元に戻すことができません。', 
		result: function(row, result) {
			var config = $.baserAjaxDataList.config;
			if(result) {
				$(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) - 1);
				$(config.pageEndNum).html(Number($(config.pageEndNum).html()) - 1);

				var rowClass = row.attr('class');
				var currentRowClassies = rowClass.split(' ');
				currentRowClassies = currentRowClassies.reverse();
				var currentRowGroupClass;
				$(currentRowClassies).each(function(){
					if(this.match(/row-group/)) {
						currentRowGroupClass = this;
						return false;
					}
				});

				$('.'+currentRowGroupClass).fadeOut(300, function(){
					$('.'+currentRowGroupClass).remove();
					if($(config.dataList+" tbody td").length) {
						$.baserAjaxDataList.initList();
						$(config.dataList+" tbody tr").removeClass('even odd');
						$.yuga.stripe();
					} else {
						var ajax = 'ajax=1';
						if( document.location.href.indexOf('?') == -1 ) {
							ajax = '?' + ajax;
						} else {
							ajax = '&' + ajax;
						}
						$.baserAjaxDataList.load(document.location.href + ajax);
					}
				});

			} else {
				$(config.alertBox).html('削除に失敗しました。');
				$(config.alertBox).fadeIn(500);
			}
		}
	};
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
	
});
/**
 * 上下移動ボタン用のクラスを解析する 
 */
function parseClass(classText, type) {
	var classies = {depth:0, group:[], current:null};
	var matches = classText.match(/depth-[0-9]+/);
  if( matches != null ) {
    classies.depth = matches[0].replace('depth-', '');
  }
  var classTexts = classText.split(' ')
	var i = 0;
	$(classTexts).each(function(){
		if(this.match(/row-group/)) {
			var rowGroup = this.replace('row-group-', '');
			classies.group.push(rowGroup);
		}
		i++;
	});
	classies.current = $(classies.group).first().get();
	return classies;
}
</script>


<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'page_categories', 'action' => 'ajax_batch')) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList"><?php $bcBaser->element('page_categories/index_list') ?></div>
