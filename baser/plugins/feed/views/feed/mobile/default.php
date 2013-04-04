<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] フィード
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<cake:nocache>
	<?php $bcBaser->cacheHeader() ?>
</cake:nocache>

<?php if(!empty($items)): ?>
	<?php foreach($items as $key => $item): ?>
<span style="color:#8ABE08">■</span> <?php echo date("y.m.d",strtotime($item['pubDate']['value'])); ?><br />
<a href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a>
<hr size="1" style="width:100%;height:1px;margin:5px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<?php endforeach; ?>
<?php else: ?>
<p style="text-align:center">ー</p>
<?php endif; ?>