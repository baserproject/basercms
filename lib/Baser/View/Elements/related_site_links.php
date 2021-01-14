<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * 関連サイトのリンクを表示する
 *
 * BcBaserHelper::getRelatedSiteLinks() より呼び出される
 */
if (empty($links) || count($links) <= 1) {
	return;
}
?>


<ul class="related-site-links">
	<?php foreach($links as $link): ?>
		<?php
		$class = $query = '';
		$queryArray = [];
		if ($this->request->params['Content']['url'] == $link['url']) {
			$class = ' class="current"';
		}
		if ($this->request->params['Site']['name']) {
			$queryArray[] = $this->request->params['Site']['name'] . '=off';
		}
		if ($link['prefix']) {
			$queryArray[] = $link['prefix'] . '_auto_redirect=off';
		}
		if ($queryArray) {
			$query = '?' . implode('&', $queryArray);
		}
		?>
		<li<?php echo $class ?>><?php $this->BcBaser->link($link['name'], $link['url'] . $query, ['title' => $link['name']]) ?></li>
	<?php endforeach ?>
</ul>
