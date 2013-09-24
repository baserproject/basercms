<?php
/**
 * [モバイル] レイアウト
 */
?>
<cake:nocache><?php $this->BcMobile->header() ?></cake:nocache><?php $this->BcBaser->xmlHeader() ?><?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<?php $this->BcBaser->charset() ?>
<?php $this->BcBaser->title() ?>
<?php $this->BcBaser->metaDescription() ?>
<?php $this->BcBaser->metaKeywords() ?>
</head>
<body bgcolor="#FFFFFF" id="<?php $this->BcBaser->contentsName() ?>">
	
	<?php echo $this->BcBaser->siteConfig['name'] ?>
	<?php $this->BcBaser->content() ?><br />
	<?php $this->BcBaser->element('contents_navi') ?><br />

	<?php $this->BcBaser->link('トップへ','/'.Configure::read('BcAgent.mobile.alias').'/') ?>

	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	
	<center>
		<?php $this->BcBaser->img('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")); ?>
		<?php $this->BcBaser->img('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?>
		<font size="1">(C)baserCMS</font>
	</center>

<?php $this->BcBaser->element('google_analytics') ?>
<?php $this->BcBaser->func() ?>
</body>
</html>