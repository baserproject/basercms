<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $this->BcForm->create() ?>
<?php echo $this->BcForm->hidden('id') ?>
<?php echo $this->BcForm->hidden('no') ?>
<?php echo $this->BcForm->hidden('blog_content_id') ?>

<?php $this->BcBaser->element('admin/MultiBlogPosts/form') ?>

<div class="submit">
    <?php echo $this->BcForm->button('保存', array('class' => 'button')) ?>
</div>

<?php echo $this->BcForm->end() ?>