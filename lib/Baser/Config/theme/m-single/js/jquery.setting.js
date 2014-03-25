
//headerナビ固定
$(function(){
	var nav = $('.headerNav'),
	offset = nav.offset();
			
	$(window).scroll(function () {
		if($(window).scrollTop() > offset.top) {
			nav.addClass('fixed');
		} else {
			nav.removeClass('fixed');
		}
	});
});


$(function() {
 
 // IE7以下に注意文
 if ( $.browser.msie && $.browser.version <= 7 ) {
  $('body').prepend('<div id="ie_error">あなたは <b>旧式ブラウザ(InternetExplorer7以下)をご利用中</b> です。このウェブサイトを快適に閲覧するにはブラウザを <a href="http://www.microsoft.com/japan/windows/products/winfamily/ie/function/default.mspx" target="_blank">アップグレード</a> してください。</div>');
 }



	// MOBILE MENU
	$(".headerNav h2").click(function(event) {
		event.preventDefault();
		$('.headerNav ul').slideToggle(250);
	});
	$(".headerNav ul a").click(function() {
		var browserWidth = $(window).width();
		if ((browserWidth) < '788') {
			$('.headerNav ul').slideUp(250);
		}
	});
	
 
 //アンカーリンク
	var headH = 70;
	var headHsp = 40;
	
	$('.headerNav a[href*=#] , .footerNav a[href*=#] , .pagetop a[href*=#]').click(function() {
		var brwWidth = $(window).width();
		
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
		&& location.hostname == this.hostname) {
			var $target = $(this.hash);
			$target = $target.length && $target
			|| $('[name=' + this.hash.slice(1) +']');
			if ($target.length) {
				if ((brwWidth) < '788') {
				var targetOffset = $target.offset().top - headHsp;
				} else {
				var targetOffset = $target.offset().top - headH;
				}
				$('html,body').animate({scrollTop: targetOffset}, 1000,'easeInOutCubic');
				return false;
			}
		}
	});
	
	if (location.hash) {
		var hash = location.hash;
		window.scroll(0, headH)
		$('a[href=' + hash + ']').click();
	}


});


/* mousewheel easing */
var scrolly = 0;
var speed = 200;

$('html').mousewheel(function(event, mov) {
 if(jQuery.browser.webkit){
  if (mov > 0) scrolly =  $('body').scrollTop() - speed;
  else if (mov < 0) scrolly =  $('body').scrollTop() + speed;
 } else {
  if (mov > 0) scrolly =  $('html').scrollTop() - speed;
  else if (mov < 0) scrolly =  $('html').scrollTop() + speed;
 }
 $('html,body')
  .stop()
  .animate({scrollTop: scrolly}, 'slow',$.easie(0,0,0,1));
  //イージングプラグイン使わない場合
  //.animate({ scrollTop: scrolly }, 'normal');
 return false;
});

