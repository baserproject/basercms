<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] よく使う項目　行
 */
$favorite['Favorite']['url'] = preg_replace('/^\/admin\//', '/' . BcUtil::getAdminPrefix() . '/', $favorite['Favorite']['url']);
?>


<li id="FavoriteRow<?php echo h($favorite['Favorite']['name']) ?>" class="bca-nav-favorite-list-item">
	<?php $this->BcBaser->link(
		'<span class="bca-nav-favorite-list-item-label">' . h($favorite['Favorite']['name']) . '</span>',
		$favorite['Favorite']['url'],
		['title' => h(Router::url($favorite['Favorite']['url'], true))]
	) ?>
	<?php echo $this->BcForm->input('Favorite.id.' . $favorite['Favorite']['id'], ['type' => 'hidden', 'value' => $favorite['Favorite']['id'], 'class' => 'favorite-id']) ?>
	<?php echo $this->BcForm->input('Favorite.name.' . $favorite['Favorite']['id'], ['type' => 'hidden', 'value' => $favorite['Favorite']['name'], 'class' => 'favorite-name']) ?>
	<?php echo $this->BcForm->input('Favorite.url.' . $favorite['Favorite']['id'], ['type' => 'hidden', 'value' => $favorite['Favorite']['url'], 'class' => 'favorite-url']) ?>
</li>
