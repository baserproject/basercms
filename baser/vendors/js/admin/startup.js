/* SVN FILE: $Id$ */
/**
 * 共通スタートアップ処理
 * 
 * Javascript / jQuery
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$(function(){
/**
 * ヘルプ用バルーンチップ設定
 *
 * jQuery / BeautyTips(jQueryプラグイン)が必要
 * ヘルプ対象のタグはクラス名を[help]とし、idは[help+識別子]とする。
 * バルーンチップに表示するテキストのタグは、クラス名をhelptextとし、idを[helptext+識別子]とする。
 */
	if($('.help').bt){
		$('.helptext').css('display','none');
		$.bt.options.closeWhenOthersOpen = true;
		$('.help').bt({
			trigger: 'click',
			//trigger: ['focus', 'blur'],
			positions: 'top',
			shadow: true,
			shadowOffsetX: 3,
			shadowOffsetY: 3,
			shadowBlur: 8,
			shadowColor: 'rgba(0,0,0,.2)',
			shadowOverlap: false,
			noShadowOpts: {
				strokeStyle: '#CCC',
				strokeWidth: 3
			},
			width: '360px',
			/*shrinkToFit: true,*/
			fill: '#FFF',
			spikeLength: 12,
			spikeGirth: 18,
			padding: 15,
			cornerRadius: 10,
			strokeWidth: 6, /*no stroke*/
			strokeStyle: '#CCC',
			fill: 'rgba(255, 255, 255, 1.00)',
			cssStyles: {
				fontSize: '12px',
				color: '#333300'
			},
			showTip: function(box){
				$(box).fadeIn(200);
			},
			// jQuery.uiのバージョンを1.8.14にしたところ、
			// hideイベントのフックで何故か再表示できなくなったのでコメントアウト
			/*hideTip: function(box, callback){
				$(box).animate({
					opacity: 0
				}, 200, callback);
			},*/
			contentSelector: "$('#helptext'+this.id.substring(4,this.id.length)).html()"
		});
	}

	/* 項目の表示・非表示を切り替える */
	$(".slide-trigger").click(function(){
		target = this.id+'Body';
		if($("#"+target).css('display') == 'none') {
			$("#"+target).slideDown();
		} else {
			$("#"+target).slideUp();
		}
	});
	$(".slide-body").hide();

	// 角丸クラスの登録
	if($('.corner5').corner) $('.corner5').corner("5px");
	if($('.corner10').corner) $('.corner10').corner("10px");
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox();
	if($("a[rel='popup']").colorbox) $("a[rel='popup']").colorbox({width:"60%", height:"70%", iframe:true});
});