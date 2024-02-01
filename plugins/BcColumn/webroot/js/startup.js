/*--------------------------------------------------------------------------*
 * rollOver on jQuery
 * rollOver tag:img,input
 * rollOver class:Over
 * rollOver FileName:*_o.*
 * Last modify:20081210
 * Licensed:MIT License
 * @author AkiraNISHIJIMA(http://nishiaki.probo.jp/)
 *--------------------------------------------------------------------------*/

$(function() {
    //ロールオーバー
    rollOver();
});

function rollOver(){
    var preLoad = new Object();
    $('img.Over,input.Over').not("[src*='_o.']").each(function(){
        var imgSrc = this.src;
        var fType = imgSrc.substring(imgSrc.lastIndexOf('.'));
        var imgName = imgSrc.substr(0, imgSrc.lastIndexOf('.'));
        var imgOver = imgName + '_o' + fType;
        preLoad[this.src] = new Image();
        preLoad[this.src].src = imgOver;
        $(this).hover(
            function (){
                this.src = imgOver;
            },
            function (){
                this.src = imgSrc;
            }
        );
    });
}





/*--------------------------------------------------------------------------*
 *  
 *  scroll Fixed navigation
 *  
 *  
 *--------------------------------------------------------------------------*/



$(function(){
	if($('#FixNavigator')[0]){
		var nav = $('#FixNavigator');
		var navTop = nav.offset().top;
		$(window).scroll(function(){
			var winTop = $(this).scrollTop();
			if(winTop >= navTop){
				nav.addClass('fixed')
			} else if (winTop <= navTop) {
				nav.removeClass('fixed')
			}
		});
	}
});



/*--------------------------------------------------------------------------*
 *  
 *  page top scroll fix
 *  
 *  http://www.webopixel.net/javascript/538.html
 *  
 *--------------------------------------------------------------------------*/

$(function() {
	var topBtn = $('#TopLink');	
	topBtn.hide();
	//スクロールが100に達したらボタン表示
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			topBtn.fadeIn();
		} else {
			topBtn.fadeOut();
		}
	});
	//スクロールしてトップ
    topBtn.click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 500);
		return false;
    });
});








/*--------------------------------------------------------------------------*
 *  
 *  グローバルナビ
 *  
 *--------------------------------------------------------------------------*/

$(function(){
	var baseUrl = $('#Logo a').attr('href');
	if( window.matchMedia('(max-width:768px)').matches ){
		var flg = "close";
		$("#BtnMenu img").click(function(){
			$('#GrobalNavi > ul').slideToggle();
			if(flg=="close") {
				this.src = baseUrl + "theme/bccolumn/img/sp/btn_close.png";
				flg = "open";
			} else {
				this.src = baseUrl + "theme/bccolumn/img/sp/btn_menu.png";
				flg = "close";
			}
		});
	}
})




/*--------------------------------------------------------------------------*
 *  
 *  dorop down header navigator
 *  
 *  2013 Yuko Shigeoka (komomodesign)
 *  http://www.komomo.biz/
 *  
 *--------------------------------------------------------------------------*/

$(function(){
	if( window.matchMedia('(min-width:768px)').matches ){
        init_menu();
    }
});

function init_menu(){
	$('li.dropdown-item').hover(
		function(){
			$(this).find('ul').show().animate({'opacity':'1', 'top':'0', 'easing':'linear'});
		},
		function(){
             $(this).find('ul').hide().css({'opacity':'0','top':'30px'})
		});
}



/*--------------------------------------------------------------------------*
 *  
 *  item-title accordion
 *  
 *--------------------------------------------------------------------------*/

$(function(){
	$('.item-title').click(function(){
		$(this).next().slideToggle();
		$(this).toggleClass("active-title");
	})
})




/*--------------------------------------------------------------------------*
 *  
 *  スムーススクロール
 *  
 *--------------------------------------------------------------------------*/
$(function() {
    $('a').bcScrollTo();
});
