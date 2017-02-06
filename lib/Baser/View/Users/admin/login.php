<?php
/**
 * [ADMIN] ログイン
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
if ($this->Session->check('Message.auth')) {
	$this->Session->flash('auth');
}
$userModel = Configure::read('BcAuthPrefix.' . $currentPrefix . '.userModel');
if (!$userModel) {
	$userModel = 'User';
}
list(, $userModel) = pluginSplit($userModel);
$userController = Inflector::tableize($userModel);
$this->append('script', <<< CSS_END
<style type="text/css">
#Contents {
	display: none;
}
#CreditScroller,#CreditScroller a{
	color:#333!important;
}
#Credit {
	text-align: right;
}
#CreditScrollerInner {
	margin-right:0;
}
html {
	margin-top:0;
}
#ToolBar {
	position:relative;
}
</style>
CSS_END
);
?>

<script type="text/javascript">

	window.onpageshow = loadInit;
	$(window).load(loadInit);

	function loadInit(){
		//$("#BtnLogin").removeAttr('disabled');
		var getTokenUrl = '<?php echo $this->BcBaser->getUrl('/mail/mail/ajax_get_token') ?>';
		$.ajaxSetup({cache: false});
		$.get(getTokenUrl, function(result) {
			$('input[name="data[_Token][key]"]').val(result);
			$('input[type="submit"]').removeAttr('disabled');
		});
	}
	
	$(function(){

	if($("#LoginCredit").html() == 1) {
		$("body").hide();
	}
	$("body").prepend($("#Login"));
	$("#"+$("#UserModel").html()+"Name").focus();
	changeNavi("#"+$("#UserModel").html()+"Name");
	changeNavi("#"+$("#UserModel").html()+"Password");

	$("#"+$("#UserModel").html()+"Name,#"+$("#UserModel").html()+"Password").bind('keyup', function(){
		if($(this).val()) {
			$(this).prev().hide();
		} else {
			$(this).prev().show();
		}
	});

	$("#Login").click(function(){
		changeView(false);
	});

	$("#LoginInner").click(function(e){
		if (e && e.stopPropagation) {
			e.stopPropagation();
		} else {
			window.event.cancelBubble = true;
		}
	});

	if($("#LoginCredit").html() == 1) {
		$("body").append($("<div>&nbsp;</div>").attr('id', 'Credit').show());
		$("#LoginInner").css('color', '#FFF');
		$("#HeaderInner").css('height', '50px');
		$("#Logo").css('position', 'absolute');
		$("#Logo").css('z-index', '10000');
		changeView($("#LoginCredit").html());
		// 本体がない場合にフッターが上にあがってしまうので一旦消してから表示
		$("body").fadeIn(50);
	}
		
	$("#BtnLogin").click(function(){
		$("#BtnLogin").attr('disabled', 'disabled');
		$("#UserLoginForm").submit();
	});

	function changeNavi(target){
		if($(target).val()) {
			$(target).prev().hide();
		} else {
			$(target).prev().show();
		}
	}
	function changeView(creditOn) {
		if(creditOn) {
			credit();
		} else {
			openCredit();
		}
	}
	function openCredit(completeHandler) {

		if(!$("#Credit").size()) {
			return;
		}

		$("#LoginInner").css('color', '#333');
		$("#HeaderInner").css('height', 'auto');
		$("#Logo").css('position', 'relative');
		$("#Logo").css('z-index', '0');
		$("#Wrap").css('height', '280px');
		if(completeHandler) {
			if($("#Credit").length) {
				$("#Credit").fadeOut(1000, completeHandler);
			}
			completeHandler();
		} else {
			if($("#Credit").length) {
				$("#Credit").fadeOut(1000);
			}
		}
	}
});

</script>

<div id="UserModel" style="display:none"><?php echo $userModel ?></div>
<div id="LoginCredit" style="display:none"><?php echo $this->BcBaser->siteConfig['login_credit'] ?></div>
<div id="Login">

	<div id="LoginInner">
		<?php $this->BcBaser->flash() ?>
		<h1><?php $this->BcBaser->contentsTitle() ?></h1>
		<div id="AlertMessage" class="message" style="display:none"></div>
		<?php echo $this->BcForm->create($userModel, array('action' => 'login', 'url' => array())) ?>
		<div class="float-left login-input">
			<?php echo $this->BcForm->label($userModel . '.name', 'アカウント名') ?>
			<?php echo $this->BcForm->input($userModel . '.name', array('type' => 'text', 'size' => 16, 'tabindex' => 1)) ?>
		</div>
		<div class="float-left login-input">
			<?php echo $this->BcForm->label($userModel . '.password', 'パスワード') ?>
			<?php echo $this->BcForm->input($userModel . '.password', array('type' => 'password', 'size' => 16, 'tabindex' => 2)) ?>
		</div>
		<div class="float-left submit">
			<?php echo $this->BcForm->submit('ログイン', array('div' => false, 'class' => 'btn-red button', 'id' => 'BtnLogin', 'tabindex' => 4)) ?>
		</div>
		<div class="clear login-etc">
			<?php echo $this->BcForm->input($userModel . '.saved', array('type' => 'checkbox', 'label' => 'ログイン状態を保存する', 'tabindex' => 3)) ?>　
			<?php if ($currentPrefix == 'front'): ?>
				<?php $this->BcBaser->link('パスワードを忘れた場合はこちら', array('action' => 'reset_password'), array('rel' => 'popup')) ?>
			<?php else: ?>
				<?php $this->BcBaser->link('パスワードを忘れた場合はこちら', array('action' => 'reset_password', $this->request->params['prefix'] => true), array('rel' => 'popup')) ?>
			<?php endif ?>
		</div>
		<?php echo $this->BcForm->end() ?>
	</div>

</div>
