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


<div id="FavoriteDeleteUrl" style="display: none"><?php $this->BcBaser->url(array('plugin' => null, 'controller' => 'favorites', 'action' => 'ajax_delete')) ?></div>
<div id="FavoriteAjaxSorttableUrl" style="display:none"><?php $this->BcBaser->url(array('plugin' => null, 'controller' => 'favorites', 'action' => 'update_sort')) ?></div>

<nav id="FavoriteMenu" class="bca-nav-favorite">

	<h2 class="bca-nav-favorite-title"><span class="bca-nav-favorite-title-label">お気に入り</span></h2>

	<ul class="favorite-menu-list bca-nav-favorite-list">
		<?php if (!empty($favorites)): ?>
			<?php foreach ($favorites as $favorite): ?>
				<?php $this->BcBaser->element('favorite_menu_row', array('favorite' => $favorite)) ?>
			<?php endforeach ?>

		<?php else: ?>
			<li class="no-data">新規登録ボタンよりよく使う項目を登録しておく事ができます。</li>
		<?php endif ?>
	</ul>

	<ul class="favolite-menu-tools clearfix bca-nav-favorite-functions">
		<li class="bca-nav-favorite-functions-add">
			<span id="BtnFavoriteAdd" class="btn">新規追加</span>
		</li>
		<li class="bca-nav-favorite-functions-help">
			<span id="BtnFavoriteHelp" class="btn help">ヘルプ</span>
			<div class="helptext">
				<p>よく使う項目では、新規登録ボタンで現在開いているページへのリンクを簡単にする事ができます。<br />また、登録済の項目を右クリックする事で編集・削除が行えます。</p>
			</div>
		</li>
	</ul>

</nav>

<div id="FavoriteDialog" title="よく使う項目" style="display:none">
	<?php echo $this->BcForm->create('Favorite', array('url' => array('plugin' => null, 'action' => 'ajax'))) ?>
	<?php echo $this->BcForm->input('Favorite.id', array('type' => 'hidden')) ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Favorite.name', 'タイトル') ?></dt><dd><?php echo $this->BcForm->input('Favorite.name', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
		<dt><?php echo $this->BcForm->label('Favorite.url', 'URL') ?></dt><dd><?php echo $this->BcForm->input('Favorite.url', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
	</dl>
	<?php echo $this->BcForm->end() ?>
</div>


<ul id="FavoritesMenu" class="context-menu" style="display:none">
    <li class="edit"><?php $this->BcBaser->link('編集', '#FavoriteEdit') ?></li>
    <li class="delete"><?php $this->BcBaser->link('削除', '#FavoriteDelete') ?></li>
</ul>
