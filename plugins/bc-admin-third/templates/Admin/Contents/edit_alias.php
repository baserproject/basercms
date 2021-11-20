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
use BaserCore\View\BcAdminAppView;
/**
 * [ADMIN] 統合コンテンツ編集
 * Alias Edit
 * @var BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser', 'エイリアス編集'));
$site = $this->BcAdminSite->findById($this->request->getData('Content.site_id'))->first();
$this->set('publishLink', $this->BcAdminContent->getUrl($this->request->getData('Content.url'), true, $site->useSubDomain));
?>


<?php echo $this->BcAdminForm->create($content, ['url' => ['content_id' => $this->BcAdminForm->value('Content.id')]]) ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcAdminForm->control('Content.alias_id', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('Content.site_id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('Contents/form_alias') ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
  <?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
    'class' => 'button bca-btn',
    'data-bca-btn-type' => 'save',
    'div' => false
  ]) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
