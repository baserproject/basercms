<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー Step3
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
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
			$('#dbHost').show(500);
			$('#dbUser').show(500);
			$('#dbInfo').show(500);
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '3306';
			prefix = 'bc_'
		} else if (dbType.val()=='postgres') {
			$('#dbHost').show(500);
			$('#dbUser').show(500);
			$('#dbInfo').show(500);
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '5432';
			prefix = 'bc_'
		} else if(dbType.val()=='sqlite3') {
			$('#dbHost').hide(500);
			$('#dbUser').hide(500);
			$('#dbInfo').hide(500);
			$('#checkdb').hide();
			
			$('#btnnext').removeAttr("disabled");
			$('#btnnext').show();
			dbName = 'baser';
			port = '';
			$('#InstallationDbPrefix').val('');
		} else if(dbType.val()=='csv') {
			$('#dbHost').hide(500);
			$('#dbUser').hide(500);
			$('#dbInfo').hide(500);
			$('#checkdb').hide();
			$('#btnnext').removeAttr("disabled");
			$('#btnnext').show();
			dbName = 'baser';
			port = '';
			$('#InstallationDbPrefix').val('');
		} else {
			$('#dbHost').show(500);
			$('#dbUser').show(500);
			$('#dbInfo').show(500);
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

<?php echo $bcForm->create(null, array('action' => 'step3', 'id' => 'dbsettings', 'name' => 'dbsettings')) ?>

<div class="step-3">
	
	<div class="em-box">
		データベースサーバーの場合は、データベースの接続情報を入力し接続テストを実行してください。<br />
				<strong>MySQL / PostgreSQLの場合は、データベースが存在し初期化されている必要があります。</strong>
	</div>
	<h2>データベース設定</h2>
	<div class="panel-box corner10">
		<div class="section">
			<h3>接続情報</h3>
			<ul>
				<li id="dbType"> <?php echo $bcForm->label('Installation.dbType', 'データベースタイプ');?><br />
					<?php echo $bcForm->input('Installation.dbType', array('type' => 'select', 'options' => $dbsource)) ?> </li>
				<li id="dbHost"> <?php echo $bcForm->label('Installation.dbHost', 'データベースホスト名');?><br />
					<?php echo $bcForm->input('Installation.dbHost', array('type' => 'text', 'maxlength' => '300','size' => 45)); ?> </li>
				<li id="dbUser" class="clearfix">
					<label>ログイン情報</label>
					<br />
					<div class="float-left"> <?php echo $bcForm->input('Installation.dbUsername',array('type' => 'text', 'maxlength'=>'100')); ?><br />
						<small>ユーザー名</small> </div>
					<div class="float-left"> <?php echo $bcForm->input('Installation.dbPassword',array('type' => 'text', 'maxlength'=>'100','type'=>'password')); ?><br />
						<small>パスワード</small> </div>
				</li>
				<li id="dbInfo" class="clearfix">
					<label>データベース情報</label>
					<br />
					<div class="float-left"> <?php echo $bcForm->input('Installation.dbName',array('type' => 'text', 'maxlength'=>'100')); ?><br />
						<small>データベース名</small> </div>
					<div class="float-left"> <?php echo $bcForm->input('Installation.dbPrefix',array('type' => 'text', 'size'=>'10')); ?><br />
						<small>プレフィックス</small> </div>
					<div class="float-left"> <?php echo $bcForm->input('Installation.dbPort',array('type' => 'text', 'maxlength'=>'5','size'=>5)); ?><br />
						<small>ポート</small> </div>
					<?php echo $bcForm->input('buttonclicked',array('style'=>'display:none','type'=>'hidden')); ?>
					<br style="clear:both" /><br />
					<small>※ プレフィックスは英数字とアンダースコアの組み合わせとし末尾はアンダースコアにしてください。<br />
					※ ホスト名、データベース名、ポートは実際の環境に合わせて書き換えてください。</small></li>
			</ul>
		</div>
		
		<div class="section">
			<h3>オプション</h3>
			
			<ul>
				<li><label>初期データ</label><br />
					<?php echo $bcForm->input('Installation.dbDataPattern', array('type' => 'select', 'options' => $dbDataPatterns)) ?><br /><br />
					<small>※ コアパッケージや、テーマが保有するデモンストレーション用データを選択します。<br />
						※ 初めてインストールされる方は、「nada icons（ default ）」を選択してください。</small>
				</li>
			</ul>
		</div>
		
	</div>
	
	<div class="submit">
		<?php echo $bcForm->button('戻る', array('class' => 'btn-gray button', 'id' => 'btnback')) ?>
		<?php echo $bcForm->button('接続テスト', array('class' => 'btn-orange button', 'id' => 'checkdb')) ?>
<?php if (!isset($blDBSettingsOK) || !$blDBSettingsOK): ?>
		<?php echo $bcForm->button('次のステップへ', array('class' => 'btn-red button', 'id' => 'btnnext', 'name' => 'btnnext', 'disabled' => 'disabled')) ?>
<?php else: ?>
		<?php echo $bcForm->button('次のステップへ', array('class' => 'btn-red button', 'id' => 'btnnext', 'name' => 'btnnext')) ?>
<?php endif ?>
	</div>

<?php echo $bcForm->end() ?>

</div>