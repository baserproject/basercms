<?php
/* SVN FILE: $Id$ */
/**
 * フィード
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php if(!empty($items)): ?>
	<ul>
		<?php foreach($items as $key => $item): ?>
			<li<?php if(count($items) == $key+1): ?> class="end"<?php endif; ?>>
				<span class="date"><?php echo date("Y-m-d",strtotime($item['pubDate']['value'])); ?></span><br />
				<span class="description"><a href="<?php echo $item['link']['value']; ?>"><?php echo $item['title']['value']; ?></a></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p style="text-align:center">ー</p>
<?php endif; ?>