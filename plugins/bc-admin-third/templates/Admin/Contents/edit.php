<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツ編集
 */
$this->BcAdmin->setTitle(__d('baser_core', 'コンテンツ編集'));
?>


<?php echo $this->BcAdminForm->create($content, ['novalidate' => true]) ?>
<div class="submit">
  <?php echo $this->BcAdminForm->submit(__d('baser_core', '保存'), ['class' => 'button bca-btn', 'data-bca-btn-type' => 'save', 'div' => false]) ?>
</div>
<?php echo $this->BcAdminForm->end() ?>
