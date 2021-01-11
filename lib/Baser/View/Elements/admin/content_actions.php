<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.2.0
 * @license         https://basercms.net/license/index.html
 */
/**
 * @var bool $isAvailablePreview プレビュー機能が利用可能かどうか
 * @var bool $isAvailableDelete 削除機能が利用可能かどうか
 * @var string $currentAction 現在の画面のアクションボタン
 */
$deleteButtonText = __d('baser', 'ゴミ箱');
if ($isAlias) {
	$deleteButtonText = __d('baser', '削除');
}
?>


<div class="submit">
	<?php echo $this->BcHtml->link(__d('baser', '一覧に戻る'), ['plugin' => '', 'admin' => true, 'controller' => 'contents', 'action' => 'index'], [
		'class' => 'button bca-btn',
		'data-bca-btn-type' => 'back-to-list'
	]) ?>
	<?php if ($isAvailablePreview): ?>
		<?php echo $this->BcForm->button(__d('baser', 'プレビュー'), [
			'class' => 'button bca-btn',
			'data-bca-btn-type' => 'preview',
			'id' => 'BtnPreview'
		]) ?>
	<?php endif ?>
	<?php echo $currentAction ?>
	<?php if ($isAvailableDelete): ?>
		<?php echo $this->BcForm->button($deleteButtonText, [
			'data-bca-btn-type' => 'delete',
			'data-bca-btn-size' => 'sm',
			'data-bca-btn-color' => 'danger',
			'class' => 'button bca-btn',
			'id' => 'BtnDelete'
		]) ?>
	<?php endif ?>
</div>
