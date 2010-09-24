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
	$('#checkdb,#btnnext').click( function a() {
		var db_host = $("#installationDbHost");
		var db_username = $("#installationDbUsername");
		var db_name = $("#installationDbDBName");
		var db_port = $("#installationDbPort");

		if ($('#installationDbType').val() != 'csv' &&
				$('#installationDbType').val() != 'sqlite' &&
				$('#installationDbType').val() != 'sqlite3') {

			if (db_host.val() == "") {
				alert("データベースのホスト名を入力してください。");
			} else if (db_username.val() == "") {
				alert("データベースユーザー名を入力して下さい。");
			} else if (db_name.val() == "") {
				alert("データベース名を入力して下さい。");
			} else if (db_port.val() == "") {
				alert("データベースのポートナンバーを入力して下さい。");
			} else {
				if (this.id=='btnnext') {
					$("#buttonclicked").val('createdb');
				} else {
					$("#buttonclicked").val('checkdb');
				}
				$('#dbsettings').submit();
			}
			
		} else {

			if (this.id=='btnnext') {
				$("#buttonclicked").val('createdb')
			} else {
				$("#buttonclicked").val('checkdb');
			}
			$('#dbsettings').submit();

		}
		return false;
	});

	$('#installationDbType').change( function() {
		$('#installationDbHost').val('');
		$('#installationDbUsername').val('');
		$('#installationDbPassword').val('');
		$('#installationDbDBName').val('');
		$('#installationDbPort').val('');
		initForm();
	});

	$('#createbtn').click( function() {
		$('#action').val('create');
		$('#step6form').submit();
	});

	$('#dashbtn').click( function() {
		$('#action').val('workbench');
		$('#step6form').submit();
	});
});
/**
 * フォームを初期化する
 * @return void
 */
	function initForm() {

		var dbType = $('#installationDbType');
		var port,host,dbName;

		if (dbType.val()=='mysql') {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '3306';
		} else if (dbType.val()=='postgres') {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '5432';
		} else if(dbType.val()=='mssql') {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '1433';
		} else if(dbType.val()=='sqlite' || dbType.val()=='sqlite3') {
			$('#dbHost').hide();
			$('#dbUser').hide();
			$('#dbInfo').hide();
			$('#checkdb').hide();
			$('#btnnext').attr('disabled','');
			$('#btnnext').show();
			dbName = 'baser';
			port = '';
		} else if(dbType.val()=='csv') {
			$('#dbHost').hide();
			$('#dbUser').hide();
			$('#dbInfo').hide();
			$('#checkdb').hide();
			$('#btnnext').attr('disabled','');
			$('#btnnext').show();
			dbName = 'baser';
			port = '';
		} else {
			$('#dbHost').show();
			$('#dbUser').show();
			$('#dbInfo').show();
			$('#checkdb').show();
			$('#btnnext').hide();
		}
		if(!$('#installationDbHost').val()){
			$('#installationDbHost').val(host);
		}
		if(!$('#installationDbDBName').val()){
			$('#installationDbDBName').val(dbName);
		}
		if(!$('#installationDbPort').val()){
			$('#installationDbPort').val(port);
		}
		
	}
</script>

<div id="installations">
	<h3>データベース設定</h3>
	<div>
		<p> データベースサーバーの場合は、データベース情報を入力し次に進む前に接続テストを実行して下さい。<br />
			<strong>CSV以外の場合は、データベースが存在し中身が空である必要があります。</strong> </p>
	</div>
	<div>
		<form action="step3" method="post" name='dbsettings' id="dbsettings">
			<ul>
				<li id="dbType"> <?php echo $form->label('installation.dbType', 'データベースタイプ');?><br />
					<?php echo $form->select('installation.dbType',$dbsource , $defaultdb, array(),false); ?> </li>
				<li id="dbHost"> <?php echo @$form->label('installation/dbHost', 'データベースホスト名');?><br />
					<?php echo @$form->text('installation.dbHost',array('maxlength'=>'300','size'=>45, 'value'=>$dbHost)); ?> </li>
				<li id="dbUser" class="clearfix">
					<label>ログイン情報</label>
					<br />
					<div class="float-left"> <?php echo @$form->text('installation.dbUsername',array('maxlength'=>'100')); ?><br />
						<small>ユーザー名</small> </div>
					<div class="float-left"> <?php echo @$form->text('installation.dbPassword',array('maxlength'=>'100','type'=>'password')); ?><br />
						<small>パスワード</small> </div>
				</li>
				<li id="dbInfo" class="clearfix">
					<label>データベース情報</label>
					<br />
					<div class="float-left"> <?php echo @$form->text('installation.dbDBName',array('maxlength'=>'100','value'=>$dbDBName)); ?><br />
						<small>データベース名</small> </div>
					<div style="display:none"> <?php echo @$form->text('installation.dbPrefix',array('maxlength'=>'50','value'=>$dbPrefix)); ?> <small>プレフィックス</small> </div>
					<div class="float-left"> <?php echo @$form->text('installation.dbPort',array('maxlength'=>'5','size'=>5,'value'=>$dbPort)); ?><br />
						<small>ポート</small> </div>
					<?php echo @$form->input('buttonclicked',array('style'=>'display:none','type'=>'hidden')); ?> </li>
			</ul>
		</form>
	</div>
	<div class="clearfix">
		<form action="step2" method="post" class="float-left">
			<button type="submit" class='btn-gray button' id='btnback' ><span>戻る</span></button>
		</form>
		<div class="float-left">
			<button class='btn-orange button' name="checkdb" type='submit' id='checkdb'> <span>接続テスト</span> </button>
		</div>
		<button class='btn-red button' name="btnnext" id='btnnext' type='button' <?php if (!isset($blDBSettingsOK) || !$blDBSettingsOK): ?> disabled='disabled' <?php endif ?>> <span>次のステップへ</span> </button>
	</div>
</div>
