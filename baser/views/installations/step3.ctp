<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー Step3
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
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
$(document).ready( function() {
	if ($('#btnnext').attr('disabled')) {
		$('#btnnext').hide();
	}
	initForm();
	<?php if (isset($blDBSettingsOK)): ?>$('#btnnext').show();<?php endif; ?>

	/* イベント登録 */
	$('#checkdb,#btnnext,#btnback').click( function a() {

		if (this.id=='btnnext') {
			$("#buttonclicked").val('createdb');
		} else if (this.id == 'btnback') {
			$("#buttonclicked").val('back');
		} else if (this.id == 'checkdb'){
			$("#buttonclicked").val('checkdb');
		}
		
		if (this.id != 'btnback' &&
			$('#InstallationDbType').val() != 'csv' &&
			$('#InstallationDbType').val() != 'sqlite3') {
			if ($("#InstallationDbHost").val() == "") {
				alert("データベースのホスト名を入力してください。");
				return false;
			} else if ($("#InstallationDbUsername").val() == "") {
				alert("データベースユーザー名を入力して下さい。");
				return false;
			} else if ($("#InstallationDbName").val() == "") {
				alert("データベース名を入力して下さい。");
				return false;
			} else if ($("#InstallationDbPort").val() == "") {
				alert("データベースのポートナンバーを入力して下さい。");
				return false;
			}			
		}
		
		$('#dbsettings').submit();

	});

	$('#InstallationDbType').change( function() {
		$('#InstallationDbHost').val('');
		$('#InstallationDbUsername').val('');
		$('#InstallationDbPassword').val('');
		$('#InstallationDbDBName').val('');
		$('#InstallationDbPort').val('');
		initForm();
	});

});
/**
 * フォームを初期化する
 * @return void
 */
	function initForm() {

		var dbType = $('#InstallationDbType');
		var port,host,dbName,prefix;

		if (dbType.val()=='mysql') {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '3306';
			prefix = 'bc_'
		} else if (dbType.val()=='postgres') {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '5432';
			prefix = 'bc_'
		} else if(dbType.val()=='sqlite3') {
			$('#dbHost').hide();
			$('#dbUser').hide();
			$('#dbInfo').hide();
			$('#checkdb').hide();
			$('#btnnext').attr('disabled','');
			$('#btnnext').show();
			dbName = 'baser';
			port = '';
			$('#InstallationDbPrefix').val('');
		} else if(dbType.val()=='csv') {
			$('#dbHost').hide();
			$('#dbUser').hide();
			$('#dbInfo').hide();
			$('#checkdb').hide();
			$('#btnnext').attr('disabled','');
			$('#btnnext').show();
			dbName = 'baser';
			port = '';
			$('#InstallationDbPrefix').val('');
		} else {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
		}
		if(!$('#InstallationDbHost').val()){
			$('#InstallationDbHost').val(host);
		}
		if(!$('#InstallationDbDBName').val()){
			$('#InstallationDbDBName').val(dbName);
		}
		if(!$('#InstallationDbPort').val()){
			$('#InstallationDbPort').val(port);
		}
		if(!$('#InstallationDbPrefix').val()){
			$('#InstallationDbPrefix').val(prefix);
		}
		
	}
</script>

<div id="Installations">
	<h3>データベース設定</h3>
	<div>
		<p> データベースサーバーの場合は、データベース情報を入力し次に進む前に接続テストを実行して下さい。<br />
			<strong>CSV以外の場合は、データベースが存在し中身が空である必要があります。</strong> </p>
	</div>
	<div>
		<form action="step3" method="post" name='dbsettings' id="dbsettings">
			<ul>
				<li id="dbType"> <?php echo $form->label('Installation.dbType', 'データベースタイプ');?><br />
					<?php echo $form->select('Installation.dbType',$dbsource , null, null,false); ?> </li>
				<li id="dbHost"> <?php echo $form->label('Installation.dbHost', 'データベースホスト名');?><br />
					<?php echo $form->text('Installation.dbHost',array('maxlength'=>'300','size'=>45)); ?> </li>
				<li id="dbUser" class="clearfix">
					<label>ログイン情報</label>
					<br />
					<div class="float-left"> <?php echo $form->text('Installation.dbUsername',array('maxlength'=>'100')); ?><br />
						<small>ユーザー名</small> </div>
					<div class="float-left"> <?php echo $form->text('Installation.dbPassword',array('maxlength'=>'100','type'=>'password')); ?><br />
						<small>パスワード</small> </div>
				</li>
				<li id="dbInfo" class="clearfix">
					<label>データベース情報</label>
					<br />
					<div class="float-left"> <?php echo $form->text('Installation.dbPrefix',array('size'=>'10')); ?><br />
						<small>プレフィックス</small> </div>
					<div class="float-left"> <?php echo $form->text('Installation.dbName',array('maxlength'=>'100')); ?><br />
						<small>データベース名</small> </div>
					<div class="float-left"> <?php echo $form->text('Installation.dbPort',array('maxlength'=>'5','size'=>5)); ?><br />
						<small>ポート</small> </div>
					<?php echo $form->input('buttonclicked',array('style'=>'display:none','type'=>'hidden')); ?> </li>
			</ul>
		</form>
	</div>
	<div class="clearfix">
		<div class="float-left">
			<button type="submit" class='btn-gray button' id='btnback' ><span>戻る</span></button>
		</div>
		<div class="float-left">
			<button class='btn-orange button' name="checkdb" type='submit' id='checkdb'><span>接続テスト</span></button>
		</div>
		<button class='btn-red button' name="btnnext" id='btnnext' type='button' <?php if (!isset($blDBSettingsOK) || !$blDBSettingsOK): ?> disabled='disabled' <?php endif ?>> <span>次のステップへ</span> </button>
	</div>
</div>
