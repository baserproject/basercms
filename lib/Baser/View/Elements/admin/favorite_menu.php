<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] よく使う項目
 */
?>


<div id="FavoriteDeleteUrl" style="display: none"><?php $this->BcBaser->url(['plugin' => null, 'controller' => 'favorites', 'action' => 'ajax_delete']) ?></div>
<div id="FavoriteAjaxSorttableUrl" style="display:none"><?php $this->BcBaser->url(['plugin' => null, 'controller' => 'favorites', 'action' => 'update_sort']) ?></div>

<div id="FavoriteMenu" class="cbb">

	<h2><?php $this->BcBaser->img('admin/head_favorite.png', ['alt' => __d('baser', 'よく使う項目')]) ?></h2>

	<ul class="favorite-menu-list">
		<?php if (!empty($favorites)): ?>
			<?php foreach ($favorites as $favorite): ?>
				<?php $this->BcBaser->element('favorite_menu_row', ['favorite' => $favorite]) ?>
			<?php endforeach ?>

		<?php else: ?>
			<li class="no-data">新規登録ボタンよりよく使う項目を登録しておく事ができます。</li>
		<?php endif ?>
	</ul>

	<ul class="favolite-menu-tools clearfix">
		<li><?php $this->BcBaser->img('admin/btn_add.png', ['width' => 69, 'height' => 18, 'alt' => __d('baser', '新規追加'), 'id' => 'BtnFavoriteAdd', 'class' => 'btn', 'style' => 'cursor:pointer']) ?></li>
		<li><?php $this->BcBaser->img('admin/btn_menu_help.png', ['alt' => __d('baser', 'ヘルプ'), 'width' => 60, 'height' => '18', 'class' => 'btn help', 'id' => 'BtnFavoriteHelp']) ?>
			<div class="helptext">
				<p>よく使う項目では、新規登録ボタンで現在開いているページへのリンクを簡単にする事ができます。<br />また、登録済の項目を右クリックする事で編集・削除が行えます。</p>
			</div>
		</li>
	</ul>

</div>

<div id="FavoriteDialog" title="よく使う項目" style="display:none">
	<?php echo $this->BcForm->create('Favorite', ['url' => ['plugin' => null, 'action' => 'ajax']]) ?>
	<?php echo $this->BcForm->input('Favorite.id', ['type' => 'hidden']) ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Favorite.name', __d('baser', 'タイトル')) ?></dt><dd><?php echo $this->BcForm->input('Favorite.name', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
		<dt><?php echo $this->BcForm->label('Favorite.url', 'URL') ?></dt><dd><?php echo $this->BcForm->input('Favorite.url', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
	</dl>
	<?php echo $this->BcForm->end() ?>
</div>


<ul id="FavoritesMenu" class="context-menu" style="display:none">
    <li class="edit"><?php $this->BcBaser->link(__d('baser', '編集'), '#FavoriteEdit') ?></li>
    <li class="delete"><?php $this->BcBaser->link(__d('baser', '削除'), '#FavoriteDelete') ?></li>
</ul>
