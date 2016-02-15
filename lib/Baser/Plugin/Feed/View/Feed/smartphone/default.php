<?php
/**
 * [SMARTPHONE] フィード
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if (!empty($items)): ?>
	<?php foreach ($items as $key => $item): ?>
		<?php $class = array('clearfix', 'post-' . ($key + 1)) ?>
		<?php if ($this->BcArray->first($items, $key)): ?>
			<?php $class[] = 'first' ?>
		<?php elseif ($this->BcArray->last($items, $key)): ?>
			<?php $class[] = 'last' ?>
		<?php endif ?>
		<li class="<?php echo implode(' ', $class) ?>">
			<a href="<?php echo $item['link']['value']; ?>">
				<span class="date"><?php echo date("Y.m.d", strtotime($item['pubDate']['value'])) ?></span><br />
				<span class="title"><?php echo $item['title']['value']; ?></span>
			</a>
		</li>
	<?php endforeach ?>
	<?php else: ?>
	<p style="text-align:center">－</p>
<?php endif ?>
