<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] クレジット
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$credits = Configure::read('BcCredit');
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
<?php foreach($credits as $key => $credit) : ?>
				<div class="section">
					<h2><?php echo Inflector::camelize($key) ?></h2>
<?php $i = 0 ?>
<?php foreach($credit as $key2 => $contributor): ?>
<?php $i++ ?>
<?php if($i%6==1): ?>
					<ul>
<?php endif ?>
						<li><?php if(!empty($contributor['url'])): ?><?php $bcBaser->link($contributor['name'], $contributor['url'], array('target' => '_blank')) ?><?php else: ?><?php echo $contributor['name'] ?><?php endif ?> 
							<?php if(!empty($contributor['twitter'])): ?> (<?php $bcBaser->link($contributor['twitter'], 'http://twitter.com/'.$contributor['twitter'], array('target' => '_blank')) ?>) <?php endif ?></li>
<?php if($i%6==0 || $bcArray->last($credit, $key2)): ?>
					</ul>
<?php endif ?>
<?php endforeach ?>
				</div>
<?php endforeach ?>
				
				<h1 style="margin-top:400px;">baserCMS Users Community</h1>

			</div>
		</div>
	</div>
</div>