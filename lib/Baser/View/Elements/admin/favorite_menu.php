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

<div id="FavoriteMenu" class="cbb">
	<h2>
		<?php $this->BcBaser->img('admin/head_favorite.png', ['alt' => __d('baser', 'よく使う項目')]) ?>
		<?php echo __d('baser', 'よく使う項目') ?>
	</h2>

	<ul class="favorite-menu-list">
		<?php if (!empty($favorites)): ?>
			<?php foreach($favorites as $favorite): ?>
				<?php $this->BcBaser->element('favorite_menu_row', ['favorite' => $favorite]) ?>
			<?php endforeach ?>

		<?php else: ?>
			<li class="no-data"><?php echo __d('baser', '新規登録ボタンよりよく使う項目を登録しておく事ができます。') ?></li>
		<?php endif ?>
	</ul>

	<ul class="favolite-menu-tools clearfix">
		<li><a href="javascript:void()"
			   id="BtnFavoriteAdd"><?php echo $this->BcBaser->getImg('admin/btn_add.png', ['alt' => __d('baser', '新規追加')]) . __d('baser', '新規追加') ?></a>
		</li>
		<li><a href="javascript:void()"
			   id="BtnFavoriteHelp"><?php echo $this->BcBaser->getImg('admin/btn_menu_help.png', ['alt' => __d('baser', 'ヘルプ')]) . __d('baser', 'ヘルプ') ?></a>
			<div class="helptext">
				<p><?php echo __d('baser', 'よく使う項目では、新規登録ボタンで現在開いているページへのリンクを簡単にする事ができます。<br />また、登録済の項目を右クリックする事で編集・削除が行えます。') ?></p>
			</div>
		</li>
	</ul>

</div>

<div id="FavoriteDialog" title="<?php echo __d('baser', 'よく使う項目') ?>" style="display:none">
	<?php echo $this->BcForm->create('Favorite', ['url' => ['plugin' => null, 'action' => 'ajax']]) ?>
	<?php echo $this->BcForm->input('Favorite.id', ['type' => 'hidden']) ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Favorite.name', __d('baser', 'タイトル')) ?></dt>
		<dd><?php echo $this->BcForm->input('Favorite.name', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
		<dt><?php echo $this->BcForm->label('Favorite.url', 'URL') ?></dt>
		<dd><?php echo $this->BcForm->input('Favorite.url', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
	</dl>
	<?php echo $this->BcForm->end() ?>
</div>


<ul id="FavoritesMenu" class="context-menu" style="display:none">
	<li class="edit"><?php $this->BcBaser->link(__d('baser', '編集'), '#FavoriteEdit') ?></li>
	<li class="delete"><?php $this->BcBaser->link(__d('baser', '削除'), '#FavoriteDelete') ?></li>
</ul>
