<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー Step3
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
	<?php if (!empty($blDBSettingsOK)): ?>$('#btnnext').show();<?php endif; ?>

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
				alert("データベースユーザー名を入力してください。");
				return false;
			} else if ($("#InstallationDbName").val() == "") {
				alert("データベース名を入力してください。");
				return false;
			} else if ($("#InstallationDbPrefix").val() == "") {
				alert("他のアプリケーションと重複しないプレフィックスを入力してください。（例）bc_");
				return false;
			} else if (!$("#InstallationDbPrefix").val().match(/[_]$/)) {
				alert("プレフィックスの末尾はアンダースコアにしてください。（例）bc_");
				return false;
			} else if (!$("#InstallationDbPrefix").val().match(/^[a-zA-z0-9_]+_$/)) {
				alert("プレフィックスは英数字とアンダースコアの組み合わせにしてください。（例）bc_");
				return false;
			} else if ($("#InstallationDbPort").val() == "") {
				alert("データベースのポートナンバーを入力してください。");
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

<?php echo $formEx->create(null, array('action' => 'step3', 'id' => 'dbsettings', 'name' => 'dbsettings')) ?>

<div id="Installations">
	<h3>データベース設定</h3>
	<div>
		<p> データベースサーバーの場合は、データベース情報を入力し次に進む前に接続テストを実行してください。<br />
			<strong>CSV以外の場合は、データベースが存在し中身が空である必要があります。</strong> </p>
	</div>
	<div style="margin-bottom:30px">
		<ul>
			<li id="dbType"> <?php echo $formEx->label('Installation.dbType', 'データベースタイプ');?><br />
				<?php echo $formEx->input('Installation.dbType', array('type' => 'select', 'options' => $dbsource)) ?> </li>
			<li id="dbHost"> <?php echo $formEx->label('Installation.dbHost', 'データベースホスト名');?><br />
				<?php echo $formEx->input('Installation.dbHost', array('type' => 'text', 'maxlength' => '300','size' => 45)); ?> </li>
			<li id="dbUser" class="clearfix">
				<label>ログイン情報</label>
				<br />
				<div class="float-left"> <?php echo $formEx->input('Installation.dbUsername',array('type' => 'text', 'maxlength'=>'100')); ?><br />
					<small>ユーザー名</small> </div>
				<div class="float-left"> <?php echo $formEx->input('Installation.dbPassword',array('type' => 'text', 'maxlength'=>'100','type'=>'password')); ?><br />
					<small>パスワード</small> </div>
			</li>
			<li id="dbInfo" class="clearfix">
				<label>データベース情報</label>
				<br />
				<div class="float-left"> <?php echo $formEx->input('Installation.dbPrefix',array('type' => 'text', 'size'=>'10')); ?><br />
					<small>プレフィックス</small> </div>
				<div class="float-left"> <?php echo $formEx->input('Installation.dbName',array('type' => 'text', 'maxlength'=>'100')); ?><br />
					<small>データベース名</small> </div>
				<div class="float-left"> <?php echo $formEx->input('Installation.dbPort',array('type' => 'text', 'maxlength'=>'5','size'=>5)); ?><br />
					<small>ポート</small> </div>
				<?php echo $formEx->input('buttonclicked',array('style'=>'display:none','type'=>'hidden')); ?>
				<br style="clear:both" />
				<small>※ プレフィックスは英数字とアンダースコアの組み合わせとし末尾はアンダースコアにしてください。</small></li>
		</ul>
<?php if (!empty($blDBSettingsOK)): ?>
		<h3>オプション</h3>
		<ul>
			<li><?php echo $formEx->input('Installation.non_demo_data', array('type'=>'checkbox', 'label'=>' デモデータを作成しない')); ?>
		</ul>
<?php endif ?>

	</div>
	<div class="clearfix">
		<div class="float-left">
			<?php echo $formEx->button('戻る', array('class' => 'btn-gray button', 'id' => 'btnback')) ?>
		</div>
		<div class="float-left">
			<?php echo $formEx->button('接続テスト', array('class' => 'btn-orange button', 'id' => 'checkdb')) ?>
		</div>
<?php if (!isset($blDBSettingsOK) || !$blDBSettingsOK): ?>
		<?php echo $formEx->button('次のステップへ', array('class' => 'btn-red button', 'id' => 'btnnext', 'name' => 'btnnext', 'disabled' => 'disabled')) ?>
<?php else: ?>
		<?php echo $formEx->button('次のステップへ', array('class' => 'btn-red button', 'id' => 'btnnext', 'name' => 'btnnext')) ?>
<?php endif ?>
	</div>
</div>

<?php echo $formEx->end() ?>