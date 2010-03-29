<?php
/* SVN FILE: $Id$ */
/**
 * インストーラー Step5
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
<?php if (isset($modrewriteenableerror)): ?>
	<div>
		<h3>Apache Rewrite モジュールの設定で問題がありました。</h3>
		<h4><?php echo $modrewriteenableerror; ?></h4>
	</div>
<?php else: ?>

	<div>
		<div class="section">
			おめでとうございます！BaserCMSのインストールが無事完了しました！
		</div>
	<?php if (isset($secure) && $secure==false): ?>

		<h3>次に進む前にサイトのセキュリティ対策を行って下さい。</h3>
		<h4>設定フォルダの権限変更を元に戻す</h4>
		<div class="section">
			インストールされたBaserCMSの設定ファイルを編集ができないように権限を755に変更して下さい。
			<ul><li><?php echo str_replace($_SERVER['DOCUMENT_ROOT'],'',CONFIGS) ?></li></ul>
		</div>
		<h4>.htaccess自動設定の為の権限変更を元に戻す</h4>
		<div class="section">
			また、.htaccessの自動設定を利用する為に権限を変更した場合も権限の変更を推奨します。
			<ul>
				<li><?php echo str_replace($_SERVER['DOCUMENT_ROOT'],'',ROOT).DS ?></li>
				<?php if(ROOT.DS != WWW_ROOT):?>
				<li><?php echo str_replace($_SERVER['DOCUMENT_ROOT'],'',WWW_ROOT) ?></li>
				<?php endif ?>
			</ul>
		</div>
	<?php endif ?>

	</div>

<div>
	<h3>次は何をしますか？</h3>
	<div class="section">    
		<ul>
            <?php if(isset($fancyurl)): ?>
                <li><a href="<?php echo str_replace('/index.php','',$fancybase.'/') ?>">トップページに移動</a></li>
                <li><a href="<?php echo $fancybase.'/' ?>admin/dashboard">管理者ダッシュボードに移動</a></li>
            <?php else: ?>
                <li><a href="<?php echo str_replace('/index.php','',$this->base.'/') ?>">トップページに移動</a></li>
                <li><a href="<?php echo $this->base.'/' ?>admin/dashboard">管理者ダッシュボードに移動</a></li>
            <?php endif ?>
		</ul>
	</div>
</div>

<?php endif ?>