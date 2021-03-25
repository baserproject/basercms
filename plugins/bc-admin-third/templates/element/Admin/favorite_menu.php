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
 * [ADMIN] よく使う項目
 */
?>


<div id="FavoriteDeleteUrl"
	 style="display: none"><?php $this->BcBaser->url(['plugin' => null, 'controller' => 'favorites', 'action' => 'ajax_delete']) ?></div>
<div id="FavoriteAjaxSorttableUrl"
	 style="display:none"><?php $this->BcBaser->url(['plugin' => null, 'controller' => 'favorites', 'action' => 'update_sort']) ?></div>

<nav id="FavoriteMenu" class="bca-nav-favorite">

	<h2 class="bca-nav-favorite-title">
		<button type="button" id="btn-favorite-expand" class="bca-collapse__btn bca-nav-favorite-title-button"
				data-bca-collapse="favorite-collapse" data-bca-target="#favoriteBody" aria-expanded="false"
				aria-controls="favoriteBody" data-bca-state="<?php echo ($favoriteBoxOpened)? "open" : '' ?>">
			<?php echo __d('baser', 'お気に入り') ?> <i class="bca-icon--chevron-down bca-nav-favorite-title-icon"></i>
		</button>
	</h2>

	<ul class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
		<?php if (!empty($favorites)): ?>
			<?php foreach($favorites as $favorite): ?>
				<?php $this->BcBaser->element('favorite_menu_row', ['favorite' => $favorite]) ?>
			<?php endforeach ?>

		<?php else: ?>
			<li class="no-data"><small><?php echo __d('baser', '「お気に入りに追加」ボタンよりお気に入りを登録しておく事ができます。') ?></small></li>
		<?php endif ?>
	</ul>

</nav>

<div id="FavoriteDialog" title="お気に入り登録" style="display:none">
	<?php echo $this->BcForm->create('Favorite', ['url' => ['plugin' => null, 'action' => 'ajax']]) ?>
	<?php echo $this->BcForm->input('Favorite.id', ['type' => 'hidden']) ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Favorite.name', __d('baser', 'タイトル')) ?></dt>
		<dd><?php echo $this->BcForm->input('Favorite.name', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
		<dt><?php echo $this->BcForm->label('Favorite.url', __d('baser', 'URL')) ?></dt>
		<dd><?php echo $this->BcForm->input('Favorite.url', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
	</dl>
	<?php echo $this->BcForm->end() ?>
</div>


<ul id="FavoritesMenu" class="context-menu" style="display:none">
	<li class="edit"><?php $this->BcBaser->link(__d('baser', '編集'), '#FavoriteEdit') ?></li>
	<li class="delete"><?php $this->BcBaser->link(__d('baser', '削除'), '#FavoriteDelete') ?></li>
</ul>
