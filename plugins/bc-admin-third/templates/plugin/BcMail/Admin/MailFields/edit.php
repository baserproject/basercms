<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] メールフィールド フォーム
 * @var \BcMail\View\MailAdminAppView $this
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @var \BcMail\Model\Entity\MailField $mailField
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcMail.admin/mail_fields/form.bundle', false);
$this->BcAdmin->setTitle(__d('baser', '{0}｜メールフィールド編集', $this->getRequest()->getAttribute('currentContent')->title));
$this->BcAdmin->setHelp('mail_fields_form');
?>


<?php echo $this->BcAdminForm->create($mailField, ['url' => [
  'controller' => 'mail_fields',
  'action' => 'edit',
  $mailContent->id,
  $mailField->id,
  'id' => false
]]) ?>
<?php echo $this->BcAdminForm->hidden('id') ?>

<?php $this->BcBaser->element('MailFields/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'id' => 'BtnSave',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(
      __d('baser', '削除'),
      ['action' => 'delete', $mailContent->id, $mailField->id],
      [
        'class' => 'bca-submit-token button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm',
        'block' => true,
        'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $mailField->name)
      ]
    ) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
