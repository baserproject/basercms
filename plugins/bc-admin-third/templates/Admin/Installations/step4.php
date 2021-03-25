<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] インストーラー Step4
 */
?>

<?php
$this->BcBaser->i18nScript([
	'message1' => __d('baser', '管理用メールアドレスを入力してください。'),
	'message2' => __d('baser', 'ユーザー名を入力してください。'),
	'message3' => __d('baser', 'ユーザー名には半角英数字とハイフン、アンダースコアのみ利用可能です。'),
	'message4' => __d('baser', 'あなたのパスワードを６文字以上で入力してください。'),
	'message5' => __d('baser', 'パスワードが確認欄のパスワードと同じではありません。'),
	'message6' => __d('baser', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')
]);
?>

<script type="text/javascript">
	$(document).ready(function () {
		$('#btnfinish,#btnback').click(function () {
			$.bcUtil.showLoader();
			var result = true;
			if (this.id == 'btnfinish') {
				$("#InstallationClicked").val('finish');
				if ($("#InstallationAdminEmail").val() == "") {
					alert(bcI18n.message1);
					result = false;
				} else if ($("#InstallationAdminUsername").val() == "") {
					alert(bcI18n.message2);
					result = false;
				} else if (!$("#InstallationAdminUsername").val().match(/^[a-zA-Z0-9\-_]+$/)) {
					alert(bcI18n.message3);
					result = false;
				} else if ($("#InstallationAdminPassword").val().length < 6) {
					alert(bcI18n.message4);
					result = false;
				} else if ($("#InstallationAdminPassword").val() != $("#InstallationAdminConfirmpassword").val()) {
					alert(bcI18n.message5);
					result = false;
				} else if (!$("#InstallationAdminPassword").val().match(/^[a-zA-Z0-9\-_ \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*]+$/)) {
					alert(bcI18n.message6);
					result = false;
				}
			} else if (this.id == 'btnback') {
				$("#InstallationClicked").val('back');
			}

			if(result) {
				$('#adminSettings').submit();
			} else {
				$.bcUtil.hideLoader();
				return false;
			}

		});
	});
</script>

<div class="step-4">

	<div class="em-box bca-em-box">
		<?php echo __d('baser', '最後に管理情報を登録します。<br />ここで入力した管理者アカウント名やパスワードは忘れないようにしておいてください。') ?>
	</div>

	<h2 class="bca-main__heading"><?php echo __d('baser', '管理ユーザー登録') ?></h2>

	<?php echo $this->BcForm->create(null, ['url' => ['controller' => 'installations', 'action' => 'step4'], 'id' => 'adminSettings', 'name' => 'adminSettings']) ?>

	<div class="panel-box bca-panel-box corner10">
		<div class="section">
			<ul>
				<li><label><?php echo __d('baser', 'Eメールアドレス') ?></label>
					<?php echo $this->BcForm->input('Installation.admin_email', ['type' => 'text', 'size' => 44]); ?>
				</li>
				<li>
					<label><?php echo __d('baser', '管理者アカウント名') ?></label>&nbsp;<small><?php echo __d('baser', '半角英数字（ハイフン、アンダースコア含む）') ?></small><br>
					<?php echo $this->BcForm->input('Installation.admin_username', ['type' => 'text']); ?>
				</li>
				<li class="clearfix">
					<label><?php echo __d('baser', 'パスワード') ?></label>&nbsp;<small><?php echo __d('baser', '半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&amp;;{}!$*)') ?></small><br>
					<div class="float-left">
						<?php echo $this->BcForm->input('Installation.admin_password', ['type' => 'password']); ?>
					</div>
					<div class="float-left">
						<?php echo $this->BcForm->input('Installation.admin_confirmpassword', ['type' => 'password']); ?>
						<br>
						<small><?php echo __d('baser', '確認の為もう一度入力してください') ?></small>
					</div>
				</li>
			</ul>
		</div>
		<?php echo $this->BcForm->input('Installation.clicked', ['type' => 'hidden']) ?>


	</div>

	<div class="submit bca-actions">
		<?php echo $this->BcForm->button(__d('baser', '戻る'), ['type' => 'button', 'class' => 'btn-gray button bca-btn bca-actions__item', 'id' => 'btnback']) ?>
		<?php echo $this->BcForm->button(__d('baser', '完了'), ['type' => 'button', 'class' => 'btn-red button bca-btn bca-actions__item', 'id' => 'btnfinish', 'name' => 'step5', 'data-bca-btn-type' => 'save']) ?>
	</div>

	<?php echo $this->BcForm->end() ?>

</div>
