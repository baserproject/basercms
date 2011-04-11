<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー Step4
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
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

<?php echo $formEx->create(null, array('action' => 'step4', 'id' => 'adminSettings', 'name' => 'adminSettings')) ?>

<div id="Installations">
	<div>
		<h3>管理用メールアドレス登録</h3>
		<ul>
			<li><?php echo $formEx->input('Installation.admin_email', array('type' => 'text', 'size'=>44)); ?></li>
		</ul>
		<h3>管理ユーザー登録</h3>
		<p>ここで設定した管理者名とパスワードは忘れないように控えておいてください。</p>
		<ul>
			<li>
				<label>管理者名</label>&nbsp;<small>半角英数字（ハイフン、アンダースコア含む）</small><br />
				<?php echo $formEx->input('Installation.admin_username', array('type' => 'text')); ?>
			</li>
			<li class="clearfix">
				<label>パスワード</label>&nbsp;<small>半角英数字（ハイフン、アンダースコア含む）</small><br />
				<div class="float-left">
					<?php echo $formEx->input('Installation.admin_password', array('type' => 'password')); ?>
				</div>
				<div class="float-left">
					<?php echo $formEx->input('Installation.admin_confirmpassword', array('type' => 'password')); ?><br />
					<small>確認の為もう一度入力してください</small>
				</div>
			</li>
		</ul>
	</div>
	<?php echo $formEx->input('clicked', array('type' => 'hidden')) ?>
	<div class="clearfix">
		<div class="float-left">
			<?php echo $formEx->button('戻る', array('class' => 'btn-gray button', 'id' => 'btnback')) ?>
		</div>
		<div>
			<?php echo $formEx->button('完了', array('class' => 'btn-red button', 'id' => 'btnfinish', 'name' => 'step5')) ?>
		</div>
	</div>
</div>

<?php echo $formEx->end() ?>
