<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] フィード
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
$feed->saveCachetime();
?>
<cake:nocache>
	<?php $feed->cacheHeader() ?>
</cake:nocache>

<?php if(!empty($items)): ?>
<ul>
	<?php foreach($items as $key => $item): ?>
		<?php $no = sprintf('%02d',$key+1) ?>
		<?php if($key == 0): ?>
			<?php $class = ' class="clearfix first feed'.$no.'"' ?>
		<?php elseif($key == count($items) - 1): ?>
			<?php $class = ' class="clearfix last feed'.$no.'"' ?>
		<?php else: ?>
			<?php $class = ' class="clearfix feed'.$no.'"' ?>
		<?php endif ?>
	<li<?php echo $class ?>> <span class="date"><?php echo date("Y.m.d",strtotime($item['pubDate']['value'])); ?></span><br />
		<span class="title"><a href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a></span></li>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<p style="text-align:center">ー</p>
<?php endif; ?>
