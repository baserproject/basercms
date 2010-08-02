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

<p>インストール環境の条件をチェックしました。<br />
	次に進む為には、「基本必須条件」の赤いマークを全て解決する必要があります。</p>
<div style="margin-bottom:30px">
	<h3>基本必須条件</h3>
	<ul class="section">
		<li>
			<div class='<?php if ($phpversionok) echo 'check'; else echo 'failed'; ?>'></div>
			PHPのバージョン >= <?php echo $phpminimumversion; ?>
			<p style='color:#888888;'>現在のPHPバージョン： <?php echo $phpactualversion; ?>
				<?php if (!$phpversionok): ?>
				<br />
				<small>ご利用のサーバーでは残念ながらBaserCMSを動作させる事はできません</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($configdirwritable) echo 'check'; else echo'failed'; ?>'></div>
			/app/config フォルダの書き込み権限（707 OR 777）
			<p style='color:#888888;'>
				<?php if ($configdirwritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>/app/config フォルダに書き込み権限が必要です</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($corefilewritable) echo 'check'; else echo'failed'; ?>'></div>
			/app/config/core.php ファイルの書き込み権限（606 OR 666）<br />
			<p style='color:#888888;'>
				<?php if ($corefilewritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>/app/config/core.php ファイルに書き込み権限が必要です</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($tmpdirwritable) echo 'check'; else echo'failed'; ?>'></div>
			/app/tmp フォルダの書き込み権限（707 OR 777）
			<p style='color:#888888;'>
				<?php if ($tmpdirwritable): ?>
				書き込み可
				<?php else: ?>
				書き込み不可<br />
				<small>/app/tmp フォルダに書き込み権限が必要です。</small>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($demopagesdirwritable) echo 'check'; else echo'failed'; ?>'></div>
			<?php if(ROOT.DS != WWW_ROOT):?>
			/app/webroot/themed フォルダの書き込み権限
			<?php else: ?>
			/themed フォルダの書き込み権限（707 OR 777）
			<?php endif ?>
			<p style='color:#888888;'>
				<?php if ($demopagesdirwritable): ?>
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
	<h4>スマートURL</h4>
	<div class="section">短くスマートなURLを実現するにはApache Rewriteモジュールと.htaccessの利用許可が必要です。<br />
		<ul>
			<li>スマートURL有効：http://localhost/mail/form</li>
			<li>スマートURL無効：http://localhost/index.php/mail/form</li>
		</ul>
		スマートURLを有効にする場合には、「.htaccess ファイル書き込み権限」、「.htaccess アップロード確認」のどちらかを有効にしてください。<br />
		<small>レンタルサーバーでは、ドキュメントルートに書き込み権限を与える事が難しい場合が多いと思います。<br />
		その場合、「.htaccess アップロード確認」の指示にしたがって「有効」にすれば問題ありません。</small> </div>
	<ul class="section">
		<li>
			<div class='<?php if ($modrewriteinstalled) echo 'check'; else echo 'failed'; ?>'></div>
			Apache Rewriteモジュール インストール状態<br />
			<p style='color:#888888;'>
				<?php if ($modrewriteinstalled) : ?>
				インストール済
				<?php else: ?>
					<?php if(!$apachegetmodules): ?>
				確認不能<br />
				<small>WEBサーバーのモジュール一覧が取得できませんでした。<br />
				サーバー環境を確認してRewriteモジュールが利用可能であれば、スマートURLは実現できますのでご安心ください。<br />
				<strong>ただし、この場合、スマートURLを有効にするためには、「.htaccess ファイル書き込み権限」が「書き込み可」であったとしても、「.htaccess アップロード確認」の項目を有効にする必要があります。</strong></small>
					<?php else: ?>
				未インストール<br />
				<small>残念ながら、現在のサーバーではスマートURLがご利用できません。ただ、BaserCMSはご利用できますのでご安心ください</small>
					<?php endif ?>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($htaccesswritable) echo 'check'; else echo 'failed'; ?>'></div>
			.htaccess ファイル書き込み権限（707 OR 777）<br />
			<p style='color:#888888;'>
				<?php if ($htaccesswritable) : ?>
				書き込み可
				<?php else: ?>
				書き込み不可（有効にするには下記２箇所のフォルダに書き込み権限を与えてください）
			<ul>
				<li><small>/ フォルダ(BaserCMSの最上位となるフォルダ)</small></li>
					<?php if(ROOT.DS != WWW_ROOT):?>
				<li><small>/app/webroot フォルダ</small></li>
					<?php endif ?>
			</ul>
				<?php endif ?>
			</p>
		</li>
		<li>
			<div class='<?php if ($htaccessExists) echo 'check'; else echo 'failed'; ?>'></div>
			.htaccess アップロード確認<br />
			<p style='color:#888888;'>
				<?php if ($htaccessExists) : ?>
				アップロード確認OK
				<?php else: ?>
				アップロード未確認<br />
					<?php if(ROOT.DS != WWW_ROOT):?>
				<small>有効にするには下記の<strong style="color:red">２箇所</strong>のフォルダ内の 「htaccess.txt」 ファイルの名称を<strong style="color:red">それぞれ</strong> 「.htaccess」 へ変更してあらかじめアップロードし、「アップロード確認実行」ボタンをクリックしてください。</small>
					<?php else: ?>
				<small>有効にするには下記のフォルダ内の 「htaccess.txt」 ファイルの名称を「.htaccess」 へ変更してあらかじめアップロードし、「アップロード確認実行」ボタンをクリックしてください。</small>
					<?php endif ?>
			<ul>
				<li><small>/ フォルダ(BaserCMSの最上位となるフォルダ)</small></li>
					<?php if(ROOT.DS != WWW_ROOT):?>
				<li><small>/app/webroot フォルダ</small></li>
					<?php endif; ?>
			</ul>
			<a href="<?php echo str_replace('index.php','',$this->base) ?>installations/step2" class="button btn-red" onclick="if(!confirm('各フォルダへの .htaccess ファイルのアップロードは完了しましたか？\n\n実行後、404 エラーが表示される場合は、戻るボタンで戻り、アップロードの再確認を行ってください。\n\nInternal Server Error が表示されてしまう場合には、一旦、全ての .htaccess ファイルを削除し、公式の導入マニュアルを参考にRewriteBase対策を行ってください。')) return false">アップロード確認実行</a>
				<?php endif ?>
			</p>
		</li>
	</ul>
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
	<div class="section">PHPのメモリが <?php echo $phpminimummemorylimit . " MB"; ?> より低い場合、BaserCMSの全ての機能が正常に動作しない可能性があります。<br />
		<small>サーバー環境によってはPHPのメモリ上限が取得できず「0MB」となっている場合もあります。その場合、サーバー業者等へサーバースペックを直接確認してください。</small> </div>
	<ul class="section">
		<li>
			<div class='<?php if ($phpmemoryok) echo 'check'; else echo 'failed'; ?>'></div>
			PHPのメモリ上限 >= <?php echo $phpminimummemorylimit . " MB"; ?>
			<p style='color:#888888;'>現在のPHPのメモリ上限： <?php echo '&nbsp;'.$phpcurrentmemorylimit . " MB"; ?>
				<?php if (!$phpmemoryok): ?>
				<br />
				<small>php.iniの設定変更が可能であれば、memory_limit の値を<?php echo $phpminimummemorylimit . " MB"; ?>以上に設定してください</small>
				<?php endif ?>
			</p>
		</li>
	</ul>
	<h4>PHPセーフモード</h4>
	<div class="section">BaserCMSはセーフモードがOnの場合でも動作実績がありますが、完全にサポートしているわけではありません。
		正常に動作しない可能性がありますのでご注意ください。<br />
		<small>セーフモードがOnの場合、BaserCMSが内部的に利用するフォルダの生成や権限の付与に失敗する事があるので、その場合は、手動による個別対応が必要です。</small> </div>
	<ul class="section">
		<li>
			<div class='<?php if ($safemodeoff) echo 'check'; else echo 'failed'; ?>'></div>
			PHPセーフモード
			<p style='color:#888888;'>
				<?php if ($safemodeoff) : ?>
				Off
				<?php else: ?>
				On
				<?php endif ?>
			</p>
		</li>
	</ul>
</div>
<div>
	<form action='step2' method='post' style="float:left">
		<div>
			<button class='btn-orange button' id='btncheckagain'  type='submit' ><span>再チェック</span></button>
		</div>
	</form>
</div>
<div>
	<form action='step3' method='post'>
		<div>
			<button class='btn-red button' <?php if (!$blRequirementsMet): ?>style="display:none" disabled='disabled' <?php endif ?> id='btnnext' type='submit' ><span>次のステップへ</span></button>
		</div>
	</form>
</div>
