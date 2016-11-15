//================================================
//  jquery.mScroll.js
//
//  ヌルっとページ内スクロール。
//
//  使用方法
//  ※jquery.jsを先に読み込む必要があります。
//  ページ内リンク<a href="#hoge" class="foo">hogeへ</a>を記述
//  $(function(){
//    $('.foo').mScroll(opt);
//  });で実行
//	option
//		easing:easing
//		du:duration time
//
//  version 1.0.0
//　MIT license.
//
//  2012.03.27  masanori.matsumoto  新規作成
//================================================

(function($){
	$.fn.extend({
		mScroll: function(opt){
			var navi = $.support.boxModel ? navigator.appName.match(/Opera/) ? "html" : "html,body" : "body";
			//初期設定
			opt = $.extend({
				easing:'linear',
				du:500
			}, opt);

			return this.each(function(){
				$(this).click(function(){
					$("html,body").queue([]).stop();

					var aIti = $(this.hash).offset();
					$(navi).animate({
						scrollTop: aIti.top,
						scrollLeft: aIti.left
					},{
						easing: opt.easing,
						duration: opt.du
					});
					return false;
				});
			});
		}	
	})
})(jQuery);