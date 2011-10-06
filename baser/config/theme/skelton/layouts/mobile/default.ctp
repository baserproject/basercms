<?php
/**
 * [モバイル] レイアウト
 */
?>
<cake:nocache><?php $mobile->header() ?></cake:nocache><?php $baser->xmlHeader() ?><?php $baser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
</head>
<body bgcolor="#FFFFFF" id="<?php $baser->contentsName() ?>">
	
	<?php echo $baser->siteConfig['name'] ?>
	<?php echo $content_for_layout; ?><br />
	<?php $baser->element('contents_navi') ?><br />

	<?php $baser->link('トップへ','/'.Configure::read('AgentSettings.mobile.alias').'/') ?>

	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	
	<center>
		<?php $baser->img('baser.power.gif', array('alt'=> 'BaserCMS : Based Website Development Project', 'border'=> "0")); ?>
		<?php $baser->img('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?>
		<font size="1">(C)BaserCMS</font>
	</center>

<?php $baser->element('google_analytics') ?>
</body>
</html>