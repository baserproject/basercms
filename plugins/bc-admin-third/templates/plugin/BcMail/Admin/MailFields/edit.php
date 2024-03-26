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
$this->BcBaser->js('BcMail.admin/mail_fields/form.bundle', false, [
  'defer' => true
]);
$this->BcAdmin->setTitle(__d('baser_core', '{0}｜メールフィールド編集', $this->getRequest()->getAttribute('currentContent')->title));
$this->BcAdmin->setHelp('mail_fields_form');
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $this->request->getParam('pass.0')],
  'title' => __d('baser_core', '新規フィールド追加'),
]);
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
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
      'plugin' => 'BcMail',
      'controller' => 'MailFields',
      'action' => 'index',
      $mailContent->id
    ], [
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
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
      __d('baser_core', '削除'),
      ['action' => 'delete', $mailContent->id, $mailField->id],
      [
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm',
        'block' => true,
        'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $mailField->name),
        'data-bca-btn-color' => "danger"
      ]
    ) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
