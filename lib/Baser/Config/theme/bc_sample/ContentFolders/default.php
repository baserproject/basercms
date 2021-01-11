<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツフォルダ
 * 呼出箇所：コンテンツ一覧
 *
 * @var BcAppView $this
 * @var array $children 子コンテンツ
 */
?>


<h2 class="bs-contents-title"><?php echo $this->request->params['Content']['title'] ?></h2>

<?php if($children): ?>
<ul class="bs-contents-list">
	<?php foreach($children as $child): ?>
	<li class="bs-contents-list__item">
		<?php $this->BcBaser->link(
			$child['Content']['title'],
			$child['Content']['url'],
			['class' => 'bs-contents-list__item-title']
		) ?>
	</li>
	<?php endforeach ?>
</ul>
<?php endif ?>
