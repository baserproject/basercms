$(function() {
	
	//ロードされたときにbackToTopを消す
	$('.to-top').hide();
	
	//ある一定の高さまでスクロールされたらbackToTopを表示、そうでないときは非表示
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.to-top').fadeIn();
		} else {
			$('.to-top').fadeOut();
		}
	});
	
	//backToTopがクリックされたら上に戻る
	$('.to-top a').click(function() {
		$('body,html').animate({
			scrollTop:0
		})
		return false;
	});

	//TOPページslider
	$('#MainImage').bxSlider({
		auto: true,
		pager: true,
		easing: 'easeOutBounce',
		speed: 3000,
		pause:  8000
	});
	$('#top-main .pager-link').wrapInner('<span></span>');

});
