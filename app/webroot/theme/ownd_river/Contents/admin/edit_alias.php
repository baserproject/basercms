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
 * [ADMIN] 統合コンテンツ編集
 */
 ?>


<?php echo $this->BcForm->create('Content', array('url' => array('content_id' => $this->BcForm->value('Content.id')))) ?>
<?php echo $this->BcForm->input('Content.alias_id', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('Content.site_id', array('type' => 'hidden')) ?>

<?php $this->BcBaser->element('admin/contents/form_alias') ?>

<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>

<?php echo $this->BcForm->end() ?>
