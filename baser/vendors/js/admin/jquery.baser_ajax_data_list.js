/* SVN FILE: $Id$ */
/**
 * baserAjaxDataList プラグイン
 * 
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
(function($){

    $.baserAjaxDataList = {
	/**
	 * 初期値
	 */
		config: {
			dataList		: "#DataList",
			pagination		: ".pagination",
			direction		: ".btn-direction",
			listNum			: ".list-num",
			alertBox		: "#AlertMessage",
			loader			: '#Waiting',
			pageTotalNum	: '.page-total-num',
			pageStartNum	: '.page-start-num',
			pageEndNum		: '.page-end-num',
			searchBox		: '#Search',
			btnSearchSubmit	: '#BtnSearchSubmit',
			btnSearchReset	: '#BtnSearchClear',
			rowIdPrefix		: 'Row',
			flashBox		: '#flashMessage'
		},
	/**
	 * 初期化処理
	 */
		init: function(config){
			
			if(config) {
				$.extend($.baserAjaxDataList.config, config);
			}
			
			config = $.baserAjaxDataList.config;
			
			$(config.searchBox + " form").submit(function(){
				$.baserAjaxDataList.search();
				return false;
			});
			$(config.btnSearchSubmit).click(function(){
				$(config.searchBox + " form").submit();
				return false;
			});
			$(config.btnSearchReset).click(function(){
				$(config.searchBox + ' input[type="text"]').val("");
				$(config.searchBox + ' input[type="radio"], ' + config.searchBox + ' input[type="checkbox"]').removeAttr('checked');
				$(config.searchBox + ' select').val('');
				if($.baserAjaxDataList.resetSearchBox) {
					$.baserAjaxDataList.resetSearchBox();
				}
				return false;
			});
			$.baserAjaxDataList.initList();

		},
	/**
	 * リストの初期化処理
	 */
		initList: function() {
			
			var config = $.baserAjaxDataList.config;
			
			// イベントを削除
			$(config.pagination + " a, " + config.direction + ", " + config.listNum + " a").unbind();
			$(config.dataList + " tbody td").unbind();
			$(config.dataList + " tbody td.row-tools a").unbind();

			$.each(config.methods, function(){
				$(this.button).unbind();
			});
			
			// イベントを登録
			$(config.pagination + " a, " + config.direction + ", " + config.listNum + " a").click(function(){
				$.baserAjaxDataList.load($(this).attr('href'));
				return false;
			});
	
			$(config.dataList + " tbody td").hover(function(){
				$(this).parent().addClass('hoverrow');
			},function(){
				$(this).parent().removeClass('hoverrow');
			});

			// 行IDを初期化
			$.baserAjaxDataList.initRowId();
			
			$.each(config.methods, function(){

				if(this.button == null) {
					return true;
				}
				
				var methodsResult = this.result;
				var methodsCofirm = this.confirm;
				var methodsComplete = this.complete;

				// 各メソッドの初期化処理を実行
				if(this.initList) {
					this.initList();
				}

				// 各メソッドのクリックイベントを登録
				$(this.button).click(function(){

					if(methodsCofirm && !confirm(methodsCofirm)) {
						return false;
					}
					
					var config = $.baserAjaxDataList.config;
					var row = $("#"+$(this).parent().parent().attr('id'));
					
					$.ajax({
						type: "GET",
						url: $(this).attr('href'),
						dataType: "html",
						beforeSend: function() {
							$(config.alertBox).fadeOut(200);
							$(config.flashBox).fadeOut(200);
							$(config.loader).show();
						},
						success: function (data) {
							$(config.loader).hide();
							if(methodsResult) {
								methodsResult(row, data);
							} else {
								if(data) {
									$.baserAjaxDataList.load(document.location.href, function(){
										if(methodsComplete) {
											$(config.alertBox).html(methodsComplete);
											$(config.alertBox).fadeIn(500);
										}
									});
								} else {
									$(config.alertBox).html('処理に失敗しました。');
									$(config.alertBox).fadeIn(500);
								}
							}
						},
						error: function() {
							$(config.loader).hide();
							$(config.alertBox).html('処理に失敗しました。');
							$(config.alertBox).fadeIn(500);
						}
					});

					return false;
					
				});
				
			});
			
			// TODO イベント化すべき
			if($.baserAjaxBatch) {
				$.baserAjaxBatch.initList();
			}
			
			if($.baserAjaxSortTable) {
				$.baserAjaxSortTable.initList();
			}
			
		},
	/**
	 * 一覧を読み込む
	 */
		load: function(url, resultHander) {

			if( url.indexOf('?') == -1 ) {
				url += '?ajax=1';
			} else {
				url += '&ajax=1';
			}
			var config = $.baserAjaxDataList.config;
			$.ajax({
				type: "POST",
				url: url,
				dataType: "html",
				beforeSend: function() {
					$(config.alertBox).fadeOut(200);
					$(config.flashBox).fadeOut(200);
					$(config.loader).show();
				},
				success: function(data){
					$(config.loader).hide();
					if(data) {
						$(config.dataList).html(data);
						$.baserAjaxDataList.initList();
						$.yuga.stripe();
					} else {
						$(config.alertBox).html('データ取得に失敗しました。');
						$(config.alertBox).fadeIn(500);
					}
				},
				error: function(result, status) {
					$(config.loader).hide();
					$(config.alertBox).html('処理に失敗しました。');
					$(config.alertBox).fadeIn(500);
				},
				complete: function() {
					if(resultHander) {
						resultHander();
					}
				}
			});

		},
	/**
	 * Ajaxで検索フォームによるデータリスト取得を行う
	 */
		search: function () {
			
			var config = $.baserAjaxDataList.config;
			$.ajax({
				type: "POST",
				url: $(config.searchBox + " form").attr('action'),
				data: $(config.searchBox + " form").serialize(),
				dataType: "html",
				beforeSend: function() {
					$(config.alertBox).fadeOut(200);
					$(config.flashBox).fadeOut(200);
					$(config.loader).show();
				},
				success: function(data){
					$(config.loader).hide();
					if(data) {
						$(config.dataList).html(data);
						$.baserAjaxDataList.initList();
						$.yuga.stripe();
					} else {
						$(config.alertBox).html('データ取得に失敗しました。');
						$(config.alertBox).fadeIn(500);
					}
				},
				error: function() {
					$(config.loader).hide();
					$(config.alertBox).html('処理に失敗しました。');
					$(config.alertBox).fadeIn(500);
				}
			});
	
		},
	/**
	 * 行（tr）のIDを初期化する
	 */
		initRowId: function() {
			var config = $.baserAjaxDataList.config;
			var i = 0;
			$(config.dataList + " tbody tr").each(function(){
				i++;
				$(this).attr('id', config.rowIdPrefix + i);
			});
		}
		
	}
	
})(jQuery);