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
 * [PUBLISH] インストーラー Step2
 */
?>


<script type="text/javascript">
	$(function () {
		$("#btnnext,#btncheckagain").click(function () {
			$.bcUtil.showLoader();
			switch (this.id) {
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

	<div class="em-box bca-em-box">
		<?php echo __d('baser', 'インストール環境の条件をチェックしました。<br />次に進む為には、「基本必須条件」の赤い項目を全て解決する必要があります。') ?>
	</div>

	<div class="section bca-section">

		<!-- basic -->
		<h2 class="bca-main__heading"><?php echo __d('baser', '基本必須条件') ?></h2>
		<div class="panel-box bca-panel-box corner10">
			<ul class="section">
				<li class='<?php if ($phpVersionOk) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', 'PHPのバージョン') ?> >= <?php echo Configure::read('BcRequire.phpVersion'); ?>
					<div class="check-result"><?php echo __d('baser', '現在のPHPバージョン') ?>
						： <?php echo $phpActualVersion; ?>
						<?php if (!$phpVersionOk): ?>
							<br/>
							<small><?php echo __d('baser', 'ご利用のサーバーでは残念ながらbaserCMSを動作させる事はできません') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($configDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', '/app/Config フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<div class="check-result">
						<?php if ($configDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<small><?php echo __d('baser', '/app/Config フォルダに書き込み権限が必要です') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($pluginDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', '/app/Plugin フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<div class="check-result">
						<?php if ($pluginDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<small><?php echo __d('baser', '/app/Plugin フォルダに書き込み権限が必要です') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($tmpDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', '/app/tmp フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<div class="check-result">
						<?php if ($tmpDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<small><?php echo __d('baser', '/app/tmp フォルダに書き込み権限が必要です。') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($pagesDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', '/app/View/Pages フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<div class="check-result">
						<?php if ($pagesDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<small><?php echo __d('baser', '/app/View/Pages フォルダに書き込み権限が必要です。') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($filesDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/files フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php else: ?>
						<?php echo __d('baser', '/files フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if ($filesDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/files フォルダに書き込み権限が必要です') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/files フォルダに書き込み権限が必要です') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($themeDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/theme フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php else: ?>
						<?php echo __d('baser', '/theme フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if ($themeDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/theme フォルダに書き込み権限が必要です') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/theme フォルダに書き込み権限が必要です') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($imgDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/img フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php else: ?>
						<?php echo __d('baser', '/img フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if ($imgDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/img フォルダに書き込み権限が必要です') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/img フォルダに書き込み権限が必要です') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($cssDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/css フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php else: ?>
						<?php echo __d('baser', '/css フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if ($cssDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/css フォルダに書き込み権限が必要です') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/css フォルダに書き込み権限が必要です') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($jsDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/js フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php else: ?>
						<?php echo __d('baser', '/js フォルダの書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if ($jsDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/js フォルダに書き込み権限が必要です') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/js フォルダに書き込み権限が必要です') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($encodingOk) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', '文字エンコーディングの設定 = UTF-8') ?>
					<div class="check-result">
						<?php echo $encoding ?><br/>
						<?php if (!$encodingOk): ?>
							<small><?php echo __d('baser', 'phpの内部文字エンコーディングがUTF-8である必要があります') ?></small>
							<br/>
							<small><?php echo __d('baser', 'php.iniで「mbstring.internal_encoding」をUTF-8に設定してください') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($gdOk) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', 'GDの利用') ?>
					<div class="check-result">
						<?php if ($gdOk): ?>
							<?php echo __d('baser', '利用可') ?>
						<?php else: ?>
							<?php echo __d('baser', '利用不可') ?><br/>
							<small><?php echo __d('baser', 'phpのGDモジュールでPNGが使える必要があります') ?></small>
							<br/>
							<small><?php echo __d('baser', 'GDモジュールをインストールするか有効にしてください') ?></small>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if ($xmlOk) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', 'DOMDocumentの利用') ?>
					<div class="check-result">
						<?php if ($xmlOk): ?>
							<?php echo __d('baser', '利用可') ?>
						<?php else: ?>
							<?php echo __d('baser', '利用不可') ?><br/>
							<small><?php echo __d('baser', 'phpのxmlモジュールでDOMDocumentが使える必要があります') ?></small>
							<br/>
							<small><?php echo __d('baser', 'xmlモジュールをインストールするか有効にしてください') ?></small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="section bca-section">
		<!-- option -->
		<h2 class="bca-main__heading"><?php echo __d('baser', 'オプション') ?></h2>

		<div class="panel-box bca-panel-box corner10">
			<h3 class="bca-panel-box__title"><?php echo __d('baser', 'ファイルデータベース') ?></h3>
			<div class="section"><p
					class="bca-main__text"><?php echo __d('baser', 'データベースサーバーが利用できない場合には、ファイルベースデータベースの SQLite を利用できます。<br>有効にするには、下記のフォルダへの書き込み権限が必要です ') ?></p>
			</div>
			<ul class="section">
				<li class='<?php if ($dbDirWritable) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', '/app/db/ の書き込み権限（707 OR 777 等、サーバー推奨がある場合はそちらに従ってください）') ?><br/>
					<div class="check-result">
						<?php if ($dbDirWritable): ?>
							<?php echo __d('baser', '書き込み可') ?>
						<?php else: ?>
							<?php echo __d('baser', '書き込み不可') ?><br/>
							<small><?php echo __d('baser', 'SQLite を利用するには、/app/db/ フォルダに書き込み権限が必要です') ?></small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>

		<div class="panel-box bca-panel-box corner10">
			<h3 class="bca-panel-box__title"><?php echo __d('baser', '管理システムの参照ファイル配置') ?></h3>
			<div class="section"><p
					class="bca-main__text"><?php echo __d('baser', 'baserCMSでは、インストール時に、管理システムより参照する、画像ファイル、CSSファイル、Javascriptファイルを、下記のパスに自動配置します。<br>既に存在する場合には上書きされてしまいますのでご注意ください。') ?></p>
			</div>
			<ul class="section">
				<li class='<?php if (!$imgAdminDirExists) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/img/admin の存在') ?>
					<?php else: ?>
						<?php echo __d('baser', '/img/admin の存在') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if (!$imgAdminDirExists): ?>
							<?php echo __d('baser', '存在しない') ?>
						<?php else: ?>
							<?php echo __d('baser', '存在する') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/img/admin フォルダが上書きされますのでご注意ください。') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/img/admin フォルダが上書きされますのでご注意ください。') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if (!$cssAdminDirExists) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/css/admin の存在') ?>
					<?php else: ?>
						<?php echo __d('baser', '/css/admin の存在') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if (!$cssAdminDirExists): ?>
							<?php echo __d('baser', '存在しない') ?>
						<?php else: ?>
							<?php echo __d('baser', '存在する') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/css/admin フォルダが上書きされますのでご注意ください。') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/css/admin フォルダが上書きされますのでご注意ください。') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
				<li class='<?php if (!$jsAdminDirExists) echo 'check'; else echo 'failed'; ?>'>
					<?php if (ROOT . DS != WWW_ROOT): ?>
						<?php echo __d('baser', '/app/webroot/js/admin の存在') ?>
					<?php else: ?>
						<?php echo __d('baser', '/js/admin の存在') ?>
					<?php endif ?>
					<div class="check-result">
						<?php if (!$jsAdminDirExists): ?>
							<?php echo __d('baser', '存在しない') ?>
						<?php else: ?>
							<?php echo __d('baser', '存在する') ?><br/>
							<?php if (ROOT . DS != WWW_ROOT): ?>
								<small><?php echo __d('baser', '/app/webroot/js/admin フォルダが上書きされますのでご注意ください。') ?></small>
							<?php else: ?>
								<small><?php echo __d('baser', '/js/admin フォルダが上書きされますのでご注意ください。') ?></small>
							<?php endif ?>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>

		<div class="panel-box bca-panel-box corner10">
			<h3 class="bca-panel-box__title"><?php echo __d('baser', 'PHPのメモリ') ?></h3>
			<div class="section"><p
					class="bca-main__text"><?php echo sprintf(__d('baser', 'PHPのメモリが %s より低い場合、baserCMSの全ての機能が正常に動作しない可能性があります。'), Configure::read('BcRequire.phpMemory') . " MB") ?>
					<br>
					<small><?php echo __d('baser', 'サーバー環境によってはPHPのメモリ上限が取得できず「0MB」となっている場合もあります。その場合、サーバー業者等へサーバースペックを直接確認してください。') ?></small>
				</p></div>
			<ul class="section">
				<li class='<?php if ($phpMemoryOk) echo 'check'; else echo 'failed'; ?>'>
					<?php echo sprintf(__d('baser', 'PHPのメモリ上限 >= %s'), Configure::read('BcRequire.phpMemory') . " MB") ?>
					<div
						class="check-result"><?php echo sprintf(__d('baser', '現在のPHPのメモリ上限： %s'), '&nbsp;' . $phpMemory . " MB") ?>
						<?php if (!$phpMemoryOk): ?>
							<br/>
							<small><?php echo sprintf(__d('baser', 'php.iniの設定変更が可能であれば、memory_limit の値を%s以上に設定してください'), Configure::read('BcRequire.phpMemory') . " MB") ?></small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>

		<div class="panel-box bca-panel-box corner10">
			<h3 class="bca-panel-box__title"><?php echo __d('baser', 'PHPセーフモード') ?></h3>
			<div class="section"><p
					class="bca-main__text"><?php echo __d('baser', 'セーフモードがOnの場合、PHPを「CGIモード」に切り替えないとbaserCMSの全ての機能を利用する事はできません。') ?>
					<br>
					<small><?php echo __d('baser', 'ページカテゴリ機能や、テーマ切り替え機能など、プログラム側でフォルダを自動生成する機能は、事前にFTPでの作業を併用する必要があります。') ?></small>
				</p>
			</div>
			<ul class="section">
				<li class='<?php if ($safeModeOff) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', 'PHPセーフモード') ?>
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
					<strong
						style="color:#E02"><?php echo __d('baser', '次のステップに進む前にセーフモードをOffに切り替えてください。</strong><br />レンタルサーバー等でセーフモードをOffにできない場合は、CGIモードに切り替えてから次のステップに進んでください。<br />サーバーによっては、最上位のフォルダにある .htaccess ファイルに次の２行を記述する事でCGIモードに切り替える事ができます。') ?>
						<br/>
				</div>
				<pre class="section">AddHandler application/x-httpd-phpcgi .php
	mod_gzip_on Off</pre>
				<div class="section">
					<strong
						style="color:#E02"><?php echo __d('baser', 'インストール中にCGIモードに切り替えた場合は、クッキーを削除した上で、「再チェック」をクリックしてください。') ?></strong>
				</div>
				<div class="section">
					<?php echo __d('baser', '上記２行を記述した際に、サーバーエラーとなってしまう場合、サーバーがCGIモードをサポートしていませんので元に戻してください。baserCMSの機能が制限されてしまいますが、次の作業を行う事でセーフモードでのインストールも可能です。<br />FTPで接続を行い、次のフォルダ内のファイルやフォルダを全てコピーした上で、フォルダ全てに書き込み権限（707 Or 777 等、サーバー推奨がある場合はそちらに従ってください）を与えます。<br />コピーと権限の変更が完了したら次のステップに進みインストールを続けます。') ?>
				</div>
				<ul class="section">
					<li><?php echo __d('baser', '/baser/config/safemode/tmp/ 内の全て　→　/app/tmp/') ?></li>
					<li><?php echo __d('baser', '/baser/config/safemode/db/ 内の全て　→　/app/db/ （SQLite を利用する場合）') ?></li>
					<li><?php echo __d('baser', '/baser/config/theme/ 内の全て　→　/app/webroot/theme/') ?></li>
				</ul>
			<?php endif ?>
		</div>
		
		<div class="panel-box bca-panel-box corner10">
			<h3 class="bca-panel-box__title"><?php echo __d('baser', '拡張モジュール') ?></h3>
			<ul class="section">
				<li class='<?php if ($zipOk) echo 'check'; else echo 'failed'; ?>'>
					<?php echo __d('baser', 'Zip') ?><br/>
					<div class="check-result">
						<?php if ($zipOk): ?>
							<?php echo __d('baser', '利用可') ?>
						<?php else: ?>
							<?php echo __d('baser', '利用不可') ?><br/>
							<small><?php echo __d('baser', 'テーマなどのzipダウンロードが制限されます。') ?></small>
						<?php endif ?>
					</div>
				</li>
			</ul>
		</div>
	</div>
		

	<form action="<?php echo $this->request->base ?>/installations/step2" method="post" id="checkenv">
		<?php echo $this->BcForm->hidden('clicked') ?>
		<div class="submit bca-actions">
			<?php echo $this->BcForm->button(__d('baser', '再チェック'), ['class' => 'btn-orange button bca-btn bca-actions__item', 'id' => 'btncheckagain']) ?>
			<?php if (!$blRequirementsMet): ?>
				<?php echo $this->BcForm->button(__d('baser', '次のステップへ'), ['class' => 'btn-red button bca-btn bca-actions__item', 'id' => 'btnnext', 'style' => 'display:none', 'data-bca-btn-type' => 'save']) ?>
			<?php else: ?>
				<?php echo $this->BcForm->button(__d('baser', '次のステップへ'), ['class' => 'btn-red button bca-btn bca-actions__item', 'id' => 'btnnext', 'data-bca-btn-type' => 'save']) ?>
			<?php endif ?>
		</div>
	</form>

</div>


