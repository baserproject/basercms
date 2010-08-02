/* SVN FILE: $Id$ */
/**
 * 共通スタートアップ処理
 * 
 * Javascript / jQuery
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$(document).ready(function(){
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
			positions: 'right',
			shadow: true,
			shadowOffsetX: 3,
			shadowOffsetY: 3,
			shadowBlur: 8,
			shadowColor: 'rgba(0,0,0,.2)',
			shadowOverlap: false,
			noShadowOpts: {
				strokeStyle: '#fff',
				strokeWidth: 2
			},
			width: '250px',
			/*shrinkToFit: true,*/
			fill: '#FFF',
			spikeLength: 12,
			spikeGirth: 18,
			padding: 10,
			cornerRadius: 10,
			strokeWidth: 3, /*no stroke*/
			strokeStyle: '#FFFFFF',
			fill: 'rgba(255, 255, 221, .8)',
			cssStyles: {
				fontSize: '12px',
				color: '#333300'
			},
			showTip: function(box){
				$(box).fadeIn(200);
			},
			hideTip: function(box, callback){
				$(box).animate({
					opacity: 0
				}, 200, callback);
			},
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
	if($('#flashMessage').corner) $('#flashMessage').corner("10px");
	if($('#authMessage').corner) $('#authMessage').corner("10px");
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox();
});