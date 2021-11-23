<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * サブサイト新規登録
 */
$this->BcBaser->i18nScript([
	'confirmMessage1' => __d('baser', "サブサイトを削除してもよろしいですか？\nサブサイトに関連しているコンテンツは全てゴミ箱に入ります。"),
	'confirmMessage2' => __d('baser', 'エイリアスを本当に変更してもいいですか？<br><br>エイリアスを変更する場合、サイト全体のURLが変更となる為、保存に時間がかかりますのでご注意ください。'),
	'confirmTitle1' => __d('baser', 'エイリアス変更')
]);
$this->BcBaser->js('admin/sites/edit', false);
?>


<?php echo $this->BcForm->create('Site') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php $this->BcBaser->element('sites/form') ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
	<div class="bca-actions__before">
		<?php echo $this->BcHtml->link(__d('baser', '一覧に戻る'), ['plugin' => '', 'admin' => true, 'controller' => 'sites', 'action' => 'index'], ['class' => 'button bca-btn', 'data-bca-btn-type' => 'back-to-list']) ?>
	</div>
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
</div>

<?php echo $this->BcForm->end() ?>
