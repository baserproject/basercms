<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * フィード表示
 * 呼出箇所：フィード読込Javascript
 *
 * フィード読込JavascriptよりAjaxとして呼び出される
 *
 * @var BcAppView $this
 * @var array $items
 */
?>


<?php if (!empty($items)): ?>
	<ul class="bs-top-post">
		<?php foreach ($items as $key => $item): ?>
			<?php
				$no = sprintf('%02d', $key + 1);
				$class = ['bs-top-post__item', 'post-' . $no];
				if($this->BcArray->first($items, $key)) {
					$class[] = 'first';
				}	elseif ($this->BcArray->last($items, $key)) {
					$class[] = 'last';
				}
			?>
			<li class="<?php echo implode(' ', $class) ?>">
				<span class="bs-top-post__item-date"><?php echo date("Y.m.d", strtotime($item['pubDate']['value'])); ?></span>
				<span class="bs-top-post__item-title"><?php $this->BcBaser->link($item['title']['value'], $item['link']['value'], ['target' => '_blank']) ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="bs-top-post-to-list"><?php $this->BcBaser->link('VIEW ALL', 'https://basercms.net', ['target' => '_blank']); ?></div>
<?php else: ?>
	<p style="text-align:center">－</p>
<?php endif; ?>
