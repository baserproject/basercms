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
 * サブサイト編集
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Site $site
 * @var bool $isMainOnCurrentDisplay
 */
$this->BcAdmin->setTitle(__d('baser_core', 'サイト編集'));
$this->BcAdmin->setHelp('sites_form');
?>


<?php echo $this->BcAdminForm->create($site) ?>

<?php $this->BcBaser->element('Sites/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), ['action' => 'index'], [
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
  <?php if(!$isMainOnCurrentDisplay): ?>
  <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser_core', '削除'),
        ['action' => 'delete', $site->id],
        ['block' => true,
          'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $site->display_name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm'
        ]
      ) ?>
  </div>
  <?php endif ?>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
