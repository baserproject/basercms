<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ログイン
 */
$userModel = Configure::read('BcAuthPrefix.' . $currentPrefix . '.userModel');
if (!$userModel) {
	$userModel = 'User';
}
list(, $userModel) = pluginSplit($userModel);
$userController = Inflector::tableize($userModel);
$this->append('script', <<< CSS_END
<style type="text/css">
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
.bca-container {
	height: auto !important;
	background: #F4F5F1;
}
.bca-crumb,
.bca-main-body-header {
	display: none;
}
</style>
CSS_END
);
?>

<script type="text/javascript">

	$(function () {

		if ($("#LoginCredit").html() == 1) {
			$("body").hide();
		}

		// $("body").prepend($("#Login"));
		changeNavi("#" + $("#UserModel").html() + "Name");
		changeNavi("#" + $("#UserModel").html() + "Password");

		// $("#"+$("#UserModel").html()+"Name,#"+$("#UserModel").html()+"Password").bind('keyup', function(){
		// 	if($(this).val()) {
		// 		$(this).prev().hide();
		// 	} else {
		// 		$(this).prev().show();
		// 	}
		// });

		$("#Login").click(function () {
			changeView(false);
		});

		$("#LoginInner").click(function (e) {
			if (e && e.stopPropagation) {
				e.stopPropagation();
			} else {
				window.event.cancelBubble = true;
			}
		});

		if ($("#LoginCredit").html() == 1) {
			$("body").append($("<div>&nbsp;</div>").attr('id', 'Credit').show());
			// $("#LoginInner").css('color', '#FFF');
			$("#HeaderInner").css('height', '50px');
			$("#Logo").css('position', 'absolute');
			$("#Logo").css('z-index', '10000');
			changeView($("#LoginCredit").html());
			// 本体がない場合にフッターが上にあがってしまうので一旦消してから表示
			$("body").fadeIn(50);
		}

		function changeNavi(target) {
			if ($(target).val()) {
				$(target).prev().hide();
			} else {
				$(target).prev().show();
			}
		}

		function changeView(creditOn) {
			if (creditOn) {
				credit();
			} else {
				openCredit();
			}
		}

		function openCredit(completeHandler) {

			if (!$("#Credit").size()) {
				return;
			}

			// $("#LoginInner").css('color', '#333');
			$("#HeaderInner").css('height', 'auto');
			$("#Logo").css('position', 'relative');
			$("#Logo").css('z-index', '0');
			$("#Wrap").css('height', '280px');
			if (completeHandler) {
				if ($("#Credit").length) {
					$("#Credit").fadeOut(1000, completeHandler);
				}
				completeHandler();
			} else {
				if ($("#Credit").length) {
					$("#Credit").fadeOut(1000);
				}
			}
		}
	});

</script>

<div id="UserModel" hidden><?php echo $userModel ?></div>
<div id="LoginCredit" hidden><?php echo $this->BcBaser->siteConfig['login_credit'] ?></div>
<div id="Login" class="bca-login">

	<div id="LoginInner">
		<?php $this->BcBaser->flash() ?>

		<h1 class="bca-login__title"><?php echo $this->BcBaser->getImg('admin/logo_large.png', ['alt' => $this->BcBaser->getContentsTitle(), 'class' => 'bca-login__logo']) ?></h1>
		<div id="AlertMessage" class="message" hidden></div>
		<?php echo $this->BcForm->create($userModel, ['url' => ['action' => 'login']]) ?>
		<div class="login-input bca-login-form-item">
			<?php echo $this->BcForm->label($userModel . '.name', __d('baser', 'アカウント名')) ?>
			<?php echo $this->BcForm->input($userModel . '.name', ['type' => 'text', 'tabindex' => 1, 'autofocus' => true]) ?>
		</div>
		<div class="login-input bca-login-form-item">
			<?php echo $this->BcForm->label($userModel . '.password', __d('baser', 'パスワード')) ?>
			<?php echo $this->BcForm->input($userModel . '.password', ['type' => 'password', 'tabindex' => 2]) ?>
		</div>
		<div class="submit bca-login-form-btn-group">
			<?php echo $this->BcForm->button(__d('baser', 'ログイン'), ['type' => 'submit', 'div' => false, 'class' => 'bca-btn--login bca-btn', 'data-bca-btn-type' => 'login', 'id' => 'BtnLogin', 'tabindex' => 4]) ?>
		</div>
		<div class="clear login-etc bca-login-form-ctrl">
			<div class="bca-login-form-checker">
				<?php echo $this->BcForm->input($userModel . '.saved', [
					'type' => 'checkbox',
					'label' => __d('baser', 'ログイン状態を保存する'),
					'class' => 'bca-checkbox__input bca-login-form-checkbox ',
					'tabindex' => 3
				]); ?>
			</div>
			<div class="bca-login-forgot-pass">
				<?php if ($currentPrefix == 'front'): ?>
					<?php $this->BcBaser->link(__d('baser', 'パスワードを忘れた場合はこちら'), ['action' => 'send_activate_url']) ?>
				<?php else: ?>
					<?php $this->BcBaser->link(__d('baser', 'パスワードを忘れた場合はこちら'), ['action' => 'send_activate_url', $this->request->params['prefix'] => true]) ?>
				<?php endif ?>
			</div>
		</div>
		<?php echo $this->BcForm->end() ?>
	</div>

</div>
