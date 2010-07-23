<?php
/* SVN FILE: $Id$ */
/**
 * [モバイル] フィード
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
 * @package			baser.plugins.feed.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<cake:nocache>
	<?php $baser->cacheHeader() ?>
</cake:nocache>
<?php if(!empty($items)): ?>
	<?php foreach($items as $key => $item): ?>
<?php //echo $emoji[151] ?>
<span style="color:#FF6600">◆</span> <?php echo date("y.m.d",strtotime($item['pubDate']['value'])); ?><br />
<a href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a>
<hr size="1" style="width:100%;height:1px;margin:5px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<?php endforeach; ?>
<?php else: ?>
<p style="text-align:center">ー</p>
<?php endif; ?>
