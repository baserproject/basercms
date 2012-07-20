<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] クレジット
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
var openedFavorite;
$(function(){
	
	var pageHeight,hideTarget;
	$("#Credit").click(function(){
		$("#Credit").fadeOut(1000);
		if($('#Login').length > 0) {
			hideTarget = "";
			$("#Wrap").css('height', '280px');
			$("#LoginInner").css('color', '#333');
		} else {
			$("#Wrap").css('height', 'auto');
			if(!openedFavorite) {
				hideTarget = "#Contents";
			} else {
				hideTarget = "#Contents, #SideBar";
			}
			$(hideTarget).fadeIn(1000);
		}
		$("#Page").css('height', pageHeight);
		$("#Page").css('height', 'auto');
		$("#Page").css('overflow', 'auto');
	});
	$("#CreditScrollerInner").click(function(e){
		if (e && e.stopPropagation) {
			e.stopPropagation();
		} else {
			window.event.cancelBubble = true;
		}
	});
	$("body").prepend($("#Credit"));
	pageHeight = $("#Page").height();
	$("#BtnCredit").click(credit);
});
/**
 * クレジットを表示する
 */
function credit(){
	
	var hideTarget
	if($("#SideBar").css('display') == 'none') {
		openedFavorite = false;
		hideTarget = "#Contents";
	} else {
		openedFavorite = true;
		hideTarget = "#Contents, #SideBar";
	}

  if(_ua.ltIE8) {
    $("#Credit").show();
    $("#Page").css('overflow', 'hidden');
    $("#Footer").hide();
    $(hideTarget).hide(0, function(){
      $("#Footer").show();
      setViewSize();
    });
    $("#CreditScroller").show();
  }
  else {
    $("#Credit").fadeIn(1000);
    $("#Page").css('overflow', 'hidden');
    $("#Footer").fadeOut(500);
    $(hideTarget).fadeOut(500, function(){
      $("#Footer").fadeIn(2000);
      setViewSize();
    });
    $("#CreditScroller").fadeIn(1000);
  }
  
  //リサイズイベント
  $(window).resize(function(){
    resizeScroll();
  });
	
  var scrollSpeed = 1;
	var height = $("#CreditScroller").height();
	var posX = $(window).height();
	var id = setInterval(function(){
		if(posX < -height + $(window).height() / 2) {
			/*posX= $(window).height();*/
			clearInterval(id);
		}
		posX -= scrollSpeed;
		$('#CreditScroller').css("margin-top",posX+"px");
	}, 40);
}

function setViewSize() {
	$("#Wrap").css('height', '280px');
	$("html").height( $(this).height() - $("#ToolBar").outerHeight()*1);
	$("#Credit").height( $("#Page").height() + $("#ToolBar").outerHeight()*1);
	$("#Credit").width( $("#Page").width());
}
/**
 * スクロールバーを非表示に
 */
function resizeScroll() {
	$("html,body").height( $(this).height() - $("#ToolBar").outerHeight()*1);
	$("#Credit").width( $("#Page").width());
	$("#Credit").height( $("#Page").height() + $("#ToolBar").outerHeight()*1);  
}
</script>

<div id="Credit">
	<div id="CreditInner">
		<div id="CreditScroller">
			<div id="CreditScrollerInner">
				
				<h1>Special Thanks Credit</h1>

				<div class="section">
					<h2>Designers</h2>
					<ul>
						<li><a href="http://blog.cgfm.jp/mutsuki/" target="_blank">Kazuko Kaneuchi</a> (<a href="http://twitter.com/mutsuking" target="_blank">mutsuking</a>)</li>
						<li><a href="http://blog.clipdesign.jp/" target="_blank">Misuzu Nakamura</a> (<a href="http://twitter.com/mutsuking" target="_blank">clipdesign</a>)</li>
					</ul>
				</div>
				
				<div class="section">
					<h2>Publishers</h2>
					<ul>
						<li><a href="http://blog.cgfm.jp/garyu/" target="_blank">Toru Kaneuchi ( @Garyuten )</a> (<a href="http://twitter.com/Garyuten" target="_blank">garyuten</a>)</li>
						<li>Ray Komomo (<a href="http://twitter.com/komomoray" target="_blank">komomoray</a>)</li>
						<li><a href="http://nishiaki.probo.jp/" target="_blank">Akira Nishijima</a> (<a href="http://twitter.com/nishiaki" target="_blank">nishiaki</a>)</li>
						<li><a href="http://d.hatena.ne.jp/dai4649/" target="_blank">Daisuke Hirata</a> (<a href="http://twitter.com/daichan4649" target="_blank">daichan4649</a>)</li>
						<li><a href="http://yourcolor.seesaa.net/" target="_blank">Ogawa</a> (<a href="http://twitter.com/a2me" target="_blank">a2me</a>)</li>
						<li><a href="http://www.skyld.net/" target="_blank">Shiraishi Takayoshi</a> (<a href="http://twitter.com/takapop" target="_blank">takapop</a>)</li>
					</ul>
					<ul>
						<li><a href="http://waka.sadist.jp/port/" target="_blank">Miki Wakata</a> (<a href="http://twitter.com/wakakame" target="_blank">wakakame</a>)</li>
						<li>Takahiro Wakita (<a href="http://twitter.com/waking" target="_blank">waking</a>)</li>
						<li><a href="http://www3.coara.or.jp/~morimori/" target="_blank">Kumi Morita</a> (<a href="http://twitter.com/kun_morita" target="_blank">kun_morita</a>)</li>
						<li><a href="http://blog.masanorihashimoto.com/" target="_blank">Masanori Hashimoto</a> (<a href="http://twitter.com/hsmt" target="_blank">hsmt</a>)</li>
						<li><a href="http://www.bulanco.net/blog/" target="_blank">Yaive</a> (<a href="http://twitter.com/Yaive" target="_blank">Yaive</a>)</li>
						<li>Kouyama (<a href="http://twitter.com/lupinkouyama" target="_blank">lupinkouyama</a>)</li>
					</ul>
					<ul>
						<li>Murajun (<a href="http://twitter.com/murajunn" target="_blank">murajunn</a>)</li>
						<li>Nobuhiko Yoshikawa (<a href="http://twitter.com/nobynoby" target="_blank">nobynoby</a>)</li>
						<li><a href="http://www.alter-ego.jp/" target="_blank">Evian</a> (<a href="http://twitter.com/evian" target="_blank">evian</a>)</li>
						<li><a href="http://apollodenki.blog97.fc2.com/" target="_blank">Apollon</a> (<a href="http://twitter.com/apollon_d" target="_blank">apollon_d</a>)</li>
						<li><a href="http://quaris.jp" target="_blank">Nagachan</a> (<a href="http://twitter.com/QuarisJP" target="_blank">QuarisJP</a>)</li>
						<li>Kou (<a href="http://twitter.com/k0567" target="_blank">k0567</a>)</li>
					</ul>
					<ul>
						<li><a href="http://www.pictnotes.jp/" target="_blank">Sunao Kiyosue</a> (<a href="http://twitter.com/itm_kiyo" target="_blank">itm_kiyo</a>)</li>
						<li><a href="http://www.clotho-web.com/wp/" target="_blank">Kazuki</a> (<a href="http://twitter.com/clothoweb" target="_blank">clothoweb</a>)</li>
						<li><a href="http://www.goreydesign.com/" target="_blank">Matuki Okawa</a> (<a href="http://twitter.com/torchright" target="_blank">torchright</a>)</li>
						<li><a href="http://one-push.info/" target="_blank">Ueda kazuhiro</a> (<a href="http://twitter.com/masyu21" target="_blank">masyu21</a>)</li>
						<li><a href="http://jungledan.info/" target="_blank">Yasuyuki Nina</a> (<a href="http://twitter.com/jungledan" target="_blank">jungledan</a>)</li>
						<li>Hiroyuki Kuwamura (<a href="http://twitter.com/staff_yg" target="_blank">staff_yg</a>)</li>
					</ul>
					<ul>
						<li><a href="http://www.clotho-web.com/wp/" target="_blank">Kazuki Arima</a> (<a href="http://twitter.com/clothoweb" target="_blank">clothoweb</a>)</li>
						<li><a href="http://motto-web.jp/" target="_blank">Goichi Maniwa</a> (<a href="http://twitter.com/goichi_m" target="_blank">goichi_m</a>)</li>
						<!-- ここからテスター -->
						<li><a href="http://www.arbalest.or.jp/" target="_blank">Masahiro Kawai</a> (<a href="http://twitter.com/m68k" target="_blank">m68k</a>)</li>
						<li><a href=" http://www.yaoto.com/" target="_blank">Yasuhiro Konishi</a> (<a href="http://twitter.com/yaotosys" target="_blank">yaotosys</a>)</li>
						<li><a href="http://www.mmpf-develop.com" target="_blank">Nobuyuki Sato</a> (<a href="http://twitter.com/mmpf_develop" target="_blank">mmpf_develop</a>)</li>
						<li><a href="http://kototoy.jp/" target="_blank">Taku Fujita</a> (<a href="http://twitter.com/takufujita" target="_blank">takufujita</a>)</li>
					</ul>
					<ul>
						<li><a href="http://aht-records.com" target="_blank">Ippei Suzuki</a> (<a href="http://twitter.com/aht_Record" target="_blank">aht_Record</a>)</li>
					</ul>
				</div>

				<div class="section">
					<h2>Developpers</h2>
					<ul>
						<li>Takason (<a href="http://twitter.com/takason" target="_blank">takason</a>)</li>
						<li>Masaharu Takishita (<a href="http://twitter.com/ecworks_masap" target="_blank">ecworks_masap</a>)</li>
						<li><a href="http://php-tips.com/" target="_blank">Takashi Nojima</a> (<a href="http://twitter.com/nojimage" target="_blank">nojimage</a>)</li>
						<li><a href="http://shin2.mogtan.net/" target="_blank">Shinji Sakai</a> (<a href="http://twitter.com/shin2" target="_blank">shin2</a>)</li>
						<li><a href="http://www.materializing.net/" target="_blank">Masakazu Fuchigami</a> (<a href="http://twitter.com/arata" target="_blank">arata</a>)</li>
						<li>Shintaro Sugimoto (<a href="http://twitter.com/withelmo" target="_blank">withelmo</a>)</li>
					</ul>
					<ul>
						<li><a href="http://exittunes.com/index2.html" target="_blank">Programmersan</a> (<a href="http://twitter.com/programmersan" target="_blank">programmersan</a>)</li>
						<li><a href="http://www.panic-net.org/" target="_blank">Min</a> (<a href="http://twitter.com/min_meou" target="_blank">min_meou</a>)</li>
						<li><a href="http://blog.grooweb.jp/" target="_blank">Youhei Nishi</a> (<a href="http://twitter.com/nippei" target="_blank">nippei</a>)</li>
						<li><a href="http://ryuring.blogspot.jp/" target="_blank">Ryuji Egashira</a> (<a href="http://twitter.com/ryuring" target="_blank">ryuring</a>)</li>
						<li>Daichi Shimoyama (<a href="http://twitter.com/daichi_shim" target="_blank">daichi_shim</a>)</li>
						<li><a href="http://www.ls-e.com" target="_blank">Tetuya Takahashi</a></li>
					</ul>
					<ul>
						<li><a href="http://exittunes.com/index2.html" target="_blank">Programmersan</a> (<a href="http://twitter.com/programmersan" target="_blank">programmersan</a>)</li>
						<li><a href="http://www.panic-net.org/" target="_blank">Min</a> (<a href="http://twitter.com/min_meou" target="_blank">min_meou</a>)</li>
						<li><a href="http://blog.grooweb.jp/" target="_blank">Youhei Nishi</a> (<a href="http://twitter.com/nippei" target="_blank">nippei</a>)</li>
						<li><a href="http://ryuring.blogspot.jp/" target="_blank">Ryuji Egashira</a> (<a href="http://twitter.com/ryuring" target="_blank">ryuring</a>)</li>
						<li>Daichi Shimoyama (<a href="http://twitter.com/daichi_shim" target="_blank">daichi_shim</a>)</li>
						<li><a href="http://www.ls-e.com" target="_blank">Tetuya Takahashi</a></li>
					</ul>
					<ul>
						<li>Masanori Matsumoto (<a href="http://twitter.com/mattun0313" target="_blank">mattun0313</a>)</li>
					</ul>
				</div>

				<h1 style="margin-top:400px;">baserCMS Users Community</h1>
				
			</div>
		</div>
	</div>
</div>

<!-- template -->
<!--<li><a href="http://publish" target="_blank">Name</a> (<a href="http://twitter.com/xxx" target="_blank">TwitterAcount</a>)</li>-->