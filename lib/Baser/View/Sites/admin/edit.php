<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サブサイト編集
 */
$this->BcBaser->js('admin/sites/edit', false);
?>


<?php echo $this->BcForm->create('Site') ?>

<?php $this->BcBaser->element('sites/form') ?>

<div class="submit">
	<?php echo $this->BcHtml->link(__d('baser', '一覧に戻る'), ['plugin' => '', 'admin' => true, 'controller' => 'sites', 'action' => 'index'], ['class' => 'button']) ?>
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php echo $this->BcForm->button(__d('baser', '削除'), ['class' => 'button', 'id' => 'BtnDelete', 'data-action' => $this->BcBaser->getUrl(['action' => 'delete'])]) ?>
</div>
