<?php
/**
 * [PUBLISH] インストーラー Step4
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

<script type="text/javascript">
$(document).ready(function(){
	$('#btnfinish,#btnback').click( function() {
		if(this.id == 'btnfinish') {
			$("#clicked").val('finish');
			if($("#InstallationAdminEmail").val() == ""){
				alert("管理用メールアドレスを入力してください。");
				return false;
			}else if($("#InstallationAdminUsername").val() == ""){
				alert("ユーザー名を入力してください。");
				return false;
			}else if(!$("#InstallationAdminUsername").val().match(/^[a-zA-Z0-9\-_]+$/)) {
				alert("ユーザー名には半角英数字とハイフン、アンダースコアのみ利用可能です。");
				return false;
			}else if($("#InstallationAdminPassword").val().length < 6){
				alert("あなたのパスワードを６文字以上で入力してください。");
				return false;
			}else if($("#InstallationAdminPassword").val() != $("#InstallationAdminConfirmpassword").val()){
				alert("パスワードが確認欄のパスワードと同じではありません。");
				return false;
			}else if(!$("#InstallationAdminPassword").val().match(/^[a-zA-Z0-9\-_]+$/)) {
				alert("パスワードには半角英数字とハイフン、アンダースコアのみ利用可能です。");
				return false;
			}
		}else if(this.id == 'btnback') {
			$("#clicked").val('back');
		}
		$('#adminSettings').submit();
	});
});
</script>

<div id="step-4">

	<div class="em-box">
		最後に管理情報を登録します。<br />
		ここで入力した管理者アカウント名やパスワードは忘れないようにしておいてください。
	</div>

	<h2>管理情報登録</h2>

	<?php echo $this->BcForm->create(null, array('action' => 'step4', 'id' => 'adminSettings', 'name' => 'adminSettings')) ?>

	<div class="panel-box corner10">
		<div class="section">
			<h3>管理用メールアドレス登録</h3>
			<ul>
				<li><?php echo $this->BcForm->input('Installation.admin_email', array('type' => 'text', 'size' => 44)); ?></li>
			</ul>
		</div>
		<div class="section">
			<h3>管理ユーザー登録</h3>
			<p>ここで設定した管理者名とパスワードは忘れないように控えておいてください。</p>
			<ul>
				<li>
					<label>管理者アカウント名</label>&nbsp;<small>半角英数字（ハイフン、アンダースコア含む）</small><br />
					<?php echo $this->BcForm->input('Installation.admin_username', array('type' => 'text')); ?>
				</li>
				<li class="clearfix">
					<label>パスワード</label>&nbsp;<small>半角英数字（ハイフン、アンダースコア含む）</small><br />
					<div class="float-left">
						<?php echo $this->BcForm->input('Installation.admin_password', array('type' => 'password')); ?>
					</div>
					<div class="float-left">
						<?php echo $this->BcForm->input('Installation.admin_confirmpassword', array('type' => 'password')); ?><br />
						<small>確認の為もう一度入力してください</small>
					</div>
				</li>
			</ul>
		</div>
		<?php echo $this->BcForm->input('clicked', array('type' => 'hidden')) ?>


	</div>

	<div class="submit">
		<?php echo $this->BcForm->button('戻る', array('type' => 'button', 'class' => 'btn-gray button', 'id' => 'btnback')) ?>
		<?php echo $this->BcForm->button('完了', array('type' => 'button', 'class' => 'btn-red button', 'id' => 'btnfinish', 'name' => 'step5')) ?>
	</div>

	<?php echo $this->BcForm->end() ?>

</div>