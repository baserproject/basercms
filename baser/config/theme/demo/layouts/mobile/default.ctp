<?php
/**
 * [モバイル] レイアウト
 */
?><cake:nocache><?php $mobile->header() ?></cake:nocache><?php $baser->xmlHeader() ?><?php $baser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
</head>
<body bgcolor="#FFFFFF" id="<?php $baser->contentsName() ?>">
<div style="color:#333333;margin:3px">
	<div style="display:-wap-marquee;text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $baser->siteConfig['name'] ?></span> </div>
	<center>
		<span style="color:#8ABE08;">Let's BaserCMS!</span>
	</center>
	<hr size="2" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:2px solid #8ABE08;" />
	<?php echo $content_for_layout; ?><br />
	<?php $baser->element('contents_navi') ?><br />
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	<span style="color:#8ABE08">◆ </span>
	<?php $baser->link('トップへ','/'.Configure::read('AgentSettings.mobile.alias').'/') ?>
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	<center>
		<?php $baser->link($baser->getImg('baser.power.gif', array('alt'=> 'BaserCMS : Based Website Development Project', 'border'=> "0")),'http://basercms.net') ?>
		<?php $baser->link($baser->getImg('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")),'http://cakephp.jp') ?>
		<font size="1">(C)BaserCMS</font>
	</center>
</div>
<?php $baser->element('google_analytics') ?>
</body>
</html>