<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * コンテンツフォルダ
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $children 子コンテンツ
 */
?>


<h2 class="bs-contents-title"><?php $this->BcBaser->contentsTitle(); ?></h2>

<?php if($children): ?>
<ul class="bs-contents-list">
	<?php foreach($children as $child): ?>
	<li class="bs-contents-list__item">
		<?php $this->BcBaser->link(
			$child->title,
			$child->url,
			['class' => 'bs-contents-list__item-title']
		) ?>
	</li>
	<?php endforeach ?>
</ul>
<?php endif ?>
