<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] インストーラー Step3
 */
?>


<script type="text/javascript">
$(document).ready( function() {
	if ($('#btnnext').prop('disabled')) {
		$('#btnnext').hide();
	}
	initForm();
	<?php if (!empty($blDBSettingsOK)): ?>$('#btnnext').show();<?php endif; ?>

	/* イベント登録 */
	$('#checkdb,#btnnext,#btnback').click( function array() {

		if (this.id=='btnnext') {
			$("#buttonclicked").val('createdb');
		} else if (this.id == 'btnback') {
			$("#buttonclicked").val('back');
		} else if (this.id == 'checkdb'){
			$("#buttonclicked").val('checkdb');
		}
		
		if (this.id != 'btnback' &&
			$('#InstallationDbType').val() != 'csv' &&
			$('#InstallationDbType').val() != 'sqlite') {
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
				alert("他のアプリケーションと重複しないプレフィックスを入力してください。（例）mysite_");
				return false;
			} else if (!$("#InstallationDbPrefix").val().match(/[_]$/)) {
				alert("プレフィックスの末尾はアンダースコアにしてください。（例）mysite_");
				return false;
			} else if (!$("#InstallationDbPrefix").val().match(/^[a-zA-z0-9_]+_$/)) {
				alert("プレフィックスは英数字とアンダースコアの組み合わせにしてください。（例）mysite_");
				return false;
			} else if ($("#InstallationDbName").val().match(/^.*\..*$/)) {
				alert("ドット（.）を含むデータベース名にはインストールできません。");
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
			prefix = 'mysite_';
		} else if (dbType.val()=='postgres') {
			$('#dbHost').show(500);
			$('#dbUser').show(500);
			$('#dbInfo').show(500);
			$('#checkdb').show();
			$('#btnnext').hide();
			host = 'localhost';
			dbName = 'baser';
			port = '5432';
			prefix = 'mysite_';
		} else if(dbType.val()=='sqlite') {
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

<?php echo $this->BcForm->create(null, ['url' => ['controller'=>'installations', 'action' => 'step3'], 'id' => 'dbsettings', 'name' => 'dbsettings']) ?>

<div class="step-3">

	<div class="em-box">
		データベースサーバーの場合は、データベースの接続情報を入力し接続テストを実行してください。<br>
		MySQL / PostgreSQLの場合は、データベースが存在し初期化されている必要があります。<br>
		<strong>既に用意したデータベースにデータが存在する場合は、初期データで上書きされてしまうので注意してください。<br>
		プレフィックスを活用しましょう。</strong>
	</div>
	<h2>データベース設定</h2>
	<div class="panel-box corner10">
		<div class="section">
			<h3>接続情報</h3>
			<ul>
				<li id="dbType"> <?php echo $this->BcForm->label('Installation.dbType', __d('baser', 'データベースタイプ')); ?><br />
					<?php echo $this->BcForm->input('Installation.dbType', ['type' => 'select', 'options' => $dbsource]) ?><br>
					<small>※ MySQL・PostgreSQL・SQLiteの中で、このサーバーで利用できるものが表示されています。</small>
					</li>
				<li id="dbHost"> <?php echo $this->BcForm->label('Installation.dbHost', __d('baser', 'データベースホスト名')); ?><br />
					<?php echo $this->BcForm->input('Installation.dbHost', ['type' => 'text', 'maxlength' => '300', 'size' => 45]); ?> </li>
				<li id="dbUser" class="clearfix">
					<label>ログイン情報</label>
					<br />
					<div class="float-left"> <?php echo $this->BcForm->input('Installation.dbUsername', ['type' => 'text', 'maxlength' => '100']); ?><br />
						<small>ユーザー名</small> </div>
					<div class="float-left"> <?php echo $this->BcForm->input('Installation.dbPassword', ['type' => 'text', 'maxlength' => '100', 'type' => 'password']); ?><br />
						<small>パスワード</small> </div>
				</li>
				<li id="dbInfo" class="clearfix">
					<label>データベース情報</label>
					<br />
					<div class="float-left"> <?php echo $this->BcForm->input('Installation.dbName', ['type' => 'text', 'maxlength' => '100']); ?><br />
						<small>データベース名</small> </div>
					<div class="float-left"> <?php echo $this->BcForm->input('Installation.dbPrefix', ['type' => 'text', 'size' => '10']); ?><br />
						<small>プレフィックス</small> </div>
					<div class="float-left"> <?php echo $this->BcForm->input('Installation.dbPort', ['type' => 'text', 'maxlength' => '5', 'size' => 5]); ?><br />
						<small>ポート</small> </div>
					<?php echo $this->BcForm->input('buttonclicked', ['style' => 'display:none', 'type' => 'hidden']); ?>
					<br style="clear:both" /><br />
					<small>※ プレフィックスは英数字とアンダースコアの組み合わせとし末尾はアンダースコアにしてください。<br />
						※ ホスト名、データベース名、ポートは実際の環境に合わせて書き換えてください。</small></li>
			</ul>
		</div>

		<div class="section">
			<h3>オプション</h3>

			<ul>
				<li><label>初期データ</label><br />
					<?php echo $this->BcForm->input('Installation.dbDataPattern', ['type' => 'select', 'options' => $dbDataPatterns]) ?><br /><br />
					<small>※ コアパッケージや、テーマが保有するデモンストレーション用データを選択します。<br />
<?php if (isset($dbDataPatterns[$this->request->data['Installation']['dbDataPattern']])): ?>
						※ 初めてインストールされる方は、「<?php echo $dbDataPatterns[$this->request->data['Installation']['dbDataPattern']]; ?>」を選択してください。</small>
<?php endif; ?>
				</li>
			</ul>
		</div>

	</div>

	<div class="submit">
		<?php echo $this->BcForm->button(__d('baser', '戻る'), ['type' => 'button', 'class' => 'btn-gray button', 'id' => 'btnback']) ?>
		<?php echo $this->BcForm->button(__d('baser', '接続テスト'), ['type' => 'button', 'class' => 'btn-orange button', 'id' => 'checkdb']) ?>
		<?php if (!isset($blDBSettingsOK) || !$blDBSettingsOK): ?>
			<?php echo $this->BcForm->button(__d('baser', '次のステップへ'), ['type' => 'button', 'class' => 'btn-red button', 'id' => 'btnnext', 'name' => 'btnnext', 'disabled' => 'disabled']) ?>
		<?php else: ?>
			<?php echo $this->BcForm->button(__d('baser', '次のステップへ'), ['type' => 'button', 'class' => 'btn-red button', 'id' => 'btnnext', 'name' => 'btnnext']) ?>
		<?php endif ?>
	</div>

	<?php echo $this->BcForm->end() ?>

</div>