<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] メールフォーム
 */
$this->BcBaser->css(['Mail.style', 'admin/jquery-ui/jquery-ui.min'], ['inline' => true]);
$this->BcBaser->js(['admin/vendors/jquery-ui-1.11.4.min', 'admin/vendors/i18n/ui.datepicker-ja'], false);
?>

<h1 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h1>

<h2 class="contents-head"><?php echo __('入力フォーム') ?></h2>

<?php if ($this->Mail->descriptionExists()): ?>
	<div class="section mail-description">
		<?php $this->Mail->description() ?>
	</div>
<?php endif ?>

<div class="section">
	<?php $this->BcBaser->flash() ?>
	<?php $this->BcBaser->element('mail_form') ?>
</div>
