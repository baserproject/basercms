<?php
/* SVN FILE: $Id$ */
/**
 * [モバイル] レイアウト
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
<?php echo "<?xml version=\"1.0\" encoding=\"Shift_JIS\"?>\n" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift-JIS" />
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
</head>
<body bgcolor="#FFFFFF" id="<?php $baser->contentsName() ?>">
<div style="color:#333333;margin:3px">
	<div style="display:-wap-marquee;text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $baser->siteConfig['name'] ?></span> </div>
	<center>
		<span style="color:#8ABE08;font-weight:bold">BaserCMS</span>
	</center>
	<hr size="2" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:2px solid #8ABE08;" />
	<?php echo $content_for_layout; ?> <br />
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	<span style="color:#8ABE08">�? </span>
	<?php $baser->link('トップへ','/'.Configure::read('Mobile.prefix').'/') ?>
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	<center>
		<font size="1">(C)BaserCMS</font>
	</center>
</div>
</body>
</html>