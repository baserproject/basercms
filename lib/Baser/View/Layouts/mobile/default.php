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
 * [MOBILE] レイアウト
 */
?><!--nocache--><?php $this->BcMobile->header() ?><!--/nocache--><?php $this->BcBaser->xmlHeader() ?><?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
	<head>
		<?php $this->BcBaser->charset() ?>
		<?php $this->BcBaser->title() ?>
		<?php $this->BcBaser->metaDescription() ?>
		<?php $this->BcBaser->metaKeywords() ?>
	</head>
	<body bgcolor="#FFFFFF" id="<?php $this->BcBaser->contentsName() ?>">
		<div style="color:#333333;margin:3px">
			<div style="display:-wap-marquee;text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $this->BcBaser->siteConfig['name'] ?></span> </div>

			<center>
				<span style="color:#8ABE08;">Let's baserCMS!</span>
			</center>

			<hr size="2" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:2px solid #8ABE08;" />

			<?php echo $content_for_layout; ?><br />
			<?php $this->BcBaser->element('contents_navi') ?><br />

			<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />

			<span style="color:#8ABE08">◆ </span>
			<?php $this->BcBaser->link(__d('baser', 'トップへ'), '/' . $this->BcBaser->getCurrentPrefix() . '/') ?>

			<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />

			<center>
				<?php $this->BcBaser->img('baser.power.gif', ['alt' => 'baserCMS : Based Website Development Project', 'border' => "0"]); ?>
				<?php $this->BcBaser->img('cake.power.gif', ['alt' => 'CakePHP(tm) : Rapid Development Framework', 'border' => "0"]); ?>
				<font size="1">(C)baserCMS</font>
			</center>

		</div>
		<?php $this->BcBaser->element('google_analytics') ?>
	</body>
</html>