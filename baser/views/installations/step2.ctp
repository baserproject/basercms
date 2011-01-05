<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー Step2
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
echo $html->css('import');
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

<p>インストール環境の条件をチェックしました。<br />
	次に進む為には、「基本必須条件」の赤いマークを全て解決する必要があります。</p>
<div style="margin-bottom:30px">
	<h3>基本必須条件</h3>
	<ul class="section">
		<li>
			<div class='<?php if ($phpVersionOk) echo 'check'; else echo 'failed'; ?>'></div>
			PHPのバージョン >= <?php echo $phpMinimumVersion; ?>
			<p style='color:#888888;'>現在のPHPバージョン： <?php echo $phpActualVersion; ?>
				<?php if (!$phpVersionOk): ?>
				<br />
				<small>ご利用のサーバーでは残念ながらBaserCMSを動作させる事はできません</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($configDirWritable) echo 'check'; else echo'failed'; ?>'></div>
			/app/config フォルダの書き込み権限（707 OR 777）
			<p style='color:#888888;'>
				<?php if ($configDirWritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>/app/config フォルダに書き込み権限が必要です</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($coreFileWritable) echo 'check'; else echo'failed'; ?>'></div>
			/app/config/core.php ファイルの書き込み権限（606 OR 666）<br />
			<p style='color:#888888;'>
				<?php if ($coreFileWritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>/app/config/core.php ファイルに書き込み権限が必要です</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($tmpDirWritable) echo 'check'; else echo'failed'; ?>'></div>
			/app/tmp フォルダの書き込み権限（707 OR 777）
			<p style='color:#888888;'>
				<?php if ($tmpDirWritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>/app/tmp フォルダに書き込み権限が必要です。</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($themeDirWritable) echo 'check'; else echo'failed'; ?>'></div>
			<?php if(ROOT.DS != WWW_ROOT):?>
			/app/webroot/themed フォルダの書き込み権限
			<?php else: ?>
			/themed フォルダの書き込み権限（707 OR 777）
			<?php endif ?>
			<p style='color:#888888;'>
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
			</p>
		</li>
	</ul>
	<h3>オプション</h3>

	<h4>ファイルデータベース</h4>
	<div class="section"> データベースサーバーが利用できない場合には、SQLiteやCSVファイル等ファイルベースのデータベースを利用できます。
		有効にするには、下記のフォルダへの書き込み権限が必要です </div>
	<ul class="section">
		<li>
			<div class='<?php if ($dbDirWritable) echo 'check'; else echo 'failed'; ?>'></div>
			/app/db/ の書き込み権限（707 OR 777）<br />
			<p style='color:#888888;'>
				<?php if ($dbDirWritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>SQLite を利用するには、/app/db/sqlite フォルダに書き込み権限が必要です</small>
				<?php endif ?>
			</p>
		</li>
	</ul>
	<h4>PHPのメモリ</h4>
	<div class="section">PHPのメモリが <?php echo $phpMinimumMemoryLimit . " MB"; ?> より低い場合、BaserCMSの全ての機能が正常に動作しない可能性があります。<br />
		<small>サーバー環境によってはPHPのメモリ上限が取得できず「0MB」となっている場合もあります。その場合、サーバー業者等へサーバースペックを直接確認してください。</small> </div>
	<ul class="section">
		<li>
			<div class='<?php if ($phpMemoryOk) echo 'check'; else echo 'failed'; ?>'></div>
			PHPのメモリ上限 >= <?php echo $phpMinimumMemoryLimit . " MB"; ?>
			<p style='color:#888888;'>現在のPHPのメモリ上限： <?php echo '&nbsp;'.$phpCurrentMemoryLimit . " MB"; ?>
				<?php if (!$phpMemoryOk): ?>
				<br />
				<small>php.iniの設定変更が可能であれば、memory_limit の値を<?php echo $phpMinimumMemoryLimit . " MB"; ?>以上に設定してください</small>
				<?php endif ?>
			</p>
		</li>
	</ul>
	<h4>PHPセーフモード</h4>
	
	<div class="section">セーフモードがOnの場合、PHPを「CGIモード」に切り替えないとBaserCMSの全ての機能を利用する事はできません。
		<small>ページカテゴリ機能や、テーマ切り替え機能など、プログラム側でフォルダを自動生成する機能は、事前にFTPでの作業を併用する必要があります。</small><br />
	</div>
	<ul class="section">
		<li>
			<div class='<?php if ($safeModeOff) echo 'check'; else echo 'failed'; ?>'></div>
			PHPセーフモード
			<p style='color:#888888;'>
				<?php if ($safeModeOff) : ?>
				Off
				<?php else: ?>
				On
				<?php endif ?>
			</p>
		</li>
	</ul>
	<?php if (!$safeModeOff) : ?>
	<div class="section">
		<strong style="color:#F00">次のステップに進む前にセーフモードをOffに切り替えてください。</strong><br />
		レンタルサーバー等でセーフモードをOffにできない場合は、CGIモードに切り替えてから次のステップに進んでください。<br />
		サーバーによっては、最上位のフォルダにある .htaccess ファイルに次の２行を記述する事でCGIモードに切り替える事ができます。<br />
	</div>
	<pre class="section">AddHandler application/x-httpd-phpcgi .php
mod_gzip_on Off</pre>
	<div class="section">
		<strong style="color:#F00">インストール中にCGIモードに切り替えた場合は、クッキーを削除した上で、「再チェック」をクリックしてください。</strong>
	</div>
	<div class="section">
		上記２行を記述した際に、サーバーエラーとなってしまう場合、サーバーがCGIモードをサポートしていませんので元に戻してください。
		BaserCMSの機能が制限されてしまいますが、次の作業を行う事でセーフモードでのインストールも可能です。<br />
		FTPで接続を行い、次のフォルダ内のファイルやフォルダを全てコピーした上で、フォルダ全てに書き込み権限（707 Or 777）を与えます。<br />
		コピーと権限の変更が完了したら次のステップに進みインストールを続けます。
	</div>
	<ul class="section"><li>/baser/config/safemode/tmp/ 内の全て　→　/app/tmp/</li>
		<li>/baser/config/safemode/db/ 内の全て　→　/app/db/ （SQLite / CSVを利用する場合）</li>
		<li>/baser/config/theme/ 内の全て　→　/app/webroot/themed/</li>
	</ul>
	<?php endif ?>
</div>
	
<form action='step2' method="post" id="checkenv">
	<div style="float:left">
		<button class='btn-orange button' id='btncheckagain'  type='submit' ><span>再チェック</span></button>
	</div>
	<div>
		<button class='btn-red button' <?php if (!$blRequirementsMet): ?>style="display:none" disabled='disabled' <?php endif ?> id='btnnext' type='submit' ><span>次のステップへ</span></button>
	</div>
	<?php echo $form->hidden('clicked') ?>
</form>