<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] インストーラー Step2
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
$(function(){
	$("#btnnext,#btncheckagain").click(function(){
		switch(this.id) {
			case 'btnnext':
				$("#clicked").val('next');
				break;
			case 'btncheckagain':
				$("#clicked").val('check');
				break;
		}
		$("#checkenv").submit();
	});
});
</script>

<div class="step2">

	<div class="em-box">
	インストール環境の条件をチェックしました。<br />
		次に進む為には、「基本必須条件」の赤い項目を全て解決する必要があります。
	</div>

	<div class="section">

		<!-- basic -->
		<h2>基本必須条件</h2>
		<div class="panel-box corner10">
			<ul class="section">
				<li class='<?php if ($phpVersionOk) echo 'check'; else echo 'failed'; ?>'>
					PHPのバージョン >= <?php echo Configure::read('BcRequire.phpVersion'); ?>
					<div class="check-result">現在のPHPバージョン： <?php echo $phpActualVersion; ?>
						<?php if (!$phpVersionOk): ?>
						<br />
						<small>ご利用のサーバーでは残念ながらbaserCMSを動作させる事はできません</small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($configDirWritable) echo 'check'; else echo'failed'; ?>'>
					/app/config フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）
					<div class="check-result">
						<?php if ($configDirWritable): ?>
						書き込み可
						<?php else: ?>
						書き込み不可<br />
						<small>/app/config フォルダに書き込み権限が必要です</small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($coreFileWritable) echo 'check'; else echo'failed'; ?>'>
					/app/config/core.php ファイルの書き込み権限（606 OR 666 等、サーバー推奨がある場合はそちらに従ってください）<br />
					<div class="check-result">
						<?php if ($coreFileWritable): ?>
						書き込み可
						<?php else: ?>
						書き込み不可<br />
						<small>/app/config/core.php ファイルに書き込み権限が必要です</small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($tmpDirWritable) echo 'check'; else echo'failed'; ?>'>
					/app/tmp フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）
					<div class="check-result">
						<?php if ($tmpDirWritable): ?>
						書き込み可
						<?php else: ?>
						書き込み不可<br />
						<small>/app/tmp フォルダに書き込み権限が必要です。</small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($filesDirWritable) echo 'check'; else echo'failed'; ?>'>
					<?php if(ROOT.DS != WWW_ROOT):?>
					/app/webroot/files フォルダの書き込み権限
					<?php else: ?>
					/files フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）
					<?php endif ?>
					<div class="check-result">
						<?php if ($filesDirWritable): ?>
						書き込み可
						<?php else: ?>
						書き込み不可<br />
						<?php if(ROOT.DS != WWW_ROOT):?>
						<small>/app/webroot/files フォルダに書き込み権限が必要です</small>
						<?php else: ?>
						<small>/files フォルダに書き込み権限が必要です</small>
						<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($themeDirWritable) echo 'check'; else echo'failed'; ?>'>
					<?php if(ROOT.DS != WWW_ROOT):?>
					/app/webroot/themed フォルダの書き込み権限
					<?php else: ?>
					/themed フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）
					<?php endif ?>
					<div class="check-result">
						<?php if ($themeDirWritable): ?>
						書き込み可
						<?php else: ?>
						書き込み不可<br />
						<?php if(ROOT.DS != WWW_ROOT):?>
						<small>/app/webroot/themed フォルダに書き込み権限が必要です</small>
						<?php else: ?>
						<small>/themed フォルダに書き込み権限が必要です</small>
						<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($encodingOk) echo 'check'; else echo'failed';	?>'>
					文字エンコーディングの設定 = UTF-8
					<div class="check-result">
						<?php echo $encoding ?><br />
						<?php if (!$encodingOk): ?>
						<small>phpの内部文字エンコーディングがUTF-8である必要があります</small>
						<br />
						<small>php.iniで「mbstring.internal_encoding」をUTF-8に設定してください</small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="section">
		<!-- option -->
		<h2>オプション</h2>

		<div class="panel-box corner10">
			<h3>ファイルデータベース</h3>
			<div class="section"> データベースサーバーが利用できない場合には、SQLiteやCSVファイル等ファイルベースのデータベースを利用できます。
				有効にするには、下記のフォルダへの書き込み権限が必要です </div>
			<ul class="section">
				<li class='<?php if ($dbDirWritable) echo 'check'; else echo 'failed'; ?>'>
					/app/db/ の書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）<br />
					<div class="check-result">
						<?php if ($dbDirWritable): ?>
						書き込み可
						<?php else: ?>
						書き込み不可<br />
						<small>SQLite を利用するには、/app/db/sqlite フォルダに書き込み権限が必要です</small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>

		<div class="panel-box corner10">
			<h3>PHPのメモリ</h3>
			<div class="section">PHPのメモリが <?php echo Configure::read('BcRequire.phpMemory') . " MB"; ?> より低い場合、baserCMSの全ての機能が正常に動作しない可能性があります。<br />
				<small>サーバー環境によってはPHPのメモリ上限が取得できず「0MB」となっている場合もあります。その場合、サーバー業者等へサーバースペックを直接確認してください。</small> </div>
			<ul class="section">
				<li class='<?php if ($phpMemoryOk) echo 'check'; else echo 'failed'; ?>'>
					PHPのメモリ上限 >= <?php echo Configure::read('BcRequire.phpMemory') . " MB"; ?>
					<div class="check-result">現在のPHPのメモリ上限： <?php echo '&nbsp;'.$phpMemory . " MB"; ?>
						<?php if (!$phpMemoryOk): ?>
						<br />
						<small>php.iniの設定変更が可能であれば、memory_limit の値を<?php echo Configure::read('BcRequire.phpMemory') . " MB"; ?>以上に設定してください</small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>

		<div class="panel-box corner10">
			<h3>PHPセーフモード</h3>
			<div class="section">セーフモードがOnの場合、PHPを「CGIモード」に切り替えないとbaserCMSの全ての機能を利用する事はできません。<br />
				<small>ページカテゴリ機能や、テーマ切り替え機能など、プログラム側でフォルダを自動生成する機能は、事前にFTPでの作業を併用する必要があります。</small><br />
			</div>
			<ul class="section">
				<li class='<?php if ($safeModeOff) echo 'check'; else echo 'failed'; ?>'>
					PHPセーフモード
					<div class="check-result">
						<?php if ($safeModeOff) : ?>
						Off
						<?php else: ?>
						On
						<?php endif ?>
					</div>
				</li>
			</ul>
			<?php if (!$safeModeOff) : ?>
			<div class="section">
				<strong style="color:#E02">次のステップに進む前にセーフモードをOffに切り替えてください。</strong><br />
				レンタルサーバー等でセーフモードをOffにできない場合は、CGIモードに切り替えてから次のステップに進んでください。<br />
				サーバーによっては、最上位のフォルダにある .htaccess ファイルに次の２行を記述する事でCGIモードに切り替える事ができます。<br />
			</div>
<pre class="section">AddHandler application/x-httpd-phpcgi .php
mod_gzip_on Off</pre>
			<div class="section">
				<strong style="color:#E02">インストール中にCGIモードに切り替えた場合は、クッキーを削除した上で、「再チェック」をクリックしてください。</strong>
			</div>
			<div class="section">
				上記２行を記述した際に、サーバーエラーとなってしまう場合、サーバーがCGIモードをサポートしていませんので元に戻してください。
				baserCMSの機能が制限されてしまいますが、次の作業を行う事でセーフモードでのインストールも可能です。<br />
				FTPで接続を行い、次のフォルダ内のファイルやフォルダを全てコピーした上で、フォルダ全てに書き込み権限（707 Or 777 等、サーバー推奨がある場合はそちらに従ってください）を与えます。<br />
				コピーと権限の変更が完了したら次のステップに進みインストールを続けます。
			</div>
			<ul class="section"><li>/baser/config/safemode/tmp/ 内の全て　→　/app/tmp/</li>
				<li>/baser/config/safemode/db/ 内の全て　→　/app/db/ （SQLite / CSVを利用する場合）</li>
				<li>/baser/config/theme/ 内の全て　→　/app/webroot/themed/</li>
			</ul>
			<?php endif ?>
		</div>
	</div>

	<?php echo $bcForm->create(null, array('action' => 'step2', 'id' => 'checkenv')) ?>
	<?php echo $bcForm->hidden('clicked') ?>

	<div class="submit">
		<?php echo $bcForm->button('再チェック', array('class' => 'btn-orange button', 'id' => 'btncheckagain')) ?>
		<?php if (!$blRequirementsMet): ?>
			<?php echo $bcForm->button('次のステップへ', array('class' => 'btn-red button', 'id' => 'btnnext', 'style' => 'display:none')) ?>
		<?php else: ?>
			<?php echo $bcForm->button('次のステップへ', array('class' => 'btn-red button', 'id' => 'btnnext')) ?>
		<?php endif ?>
	</div>

	<?php echo $bcForm->end() ?>

</div>


