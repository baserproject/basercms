<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールフィールド 一覧　行
 *
 * @var \BcMail\View\MailAdminAppView $this
 * @var \BcMail\Model\Entity\MailField $mailField
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @var int $count
 * @checked
 * @noTodo
 * @unitTest
 */
if (!$mailField->use_field) {
  $class = ' class="unpublish disablerow sortable"';
} else {
  $class = ' class="publish sortable"';
}
?>


<tr id="Row<?php echo $count ?>" <?php echo $class; ?>>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $mailField->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $mailField->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
    <?php if ($this->request->getQuery('sortmode')): ?>
      <span class="sort-handle">
        <i class="bca-btn-icon-text" data-bca-btn-type="draggable"></i>
        <?php echo __d('baser', 'ドラッグ可能') ?>
      </span>
      <?php echo $this->BcAdminForm->hidden('id' . $mailField->id, ['class' => 'id', 'value' => $mailField->id]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $mailField->no ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php $this->BcBaser->link($mailField->field_name, ['action' => 'edit', $mailContent->id, $mailField->id]) ?>
    <br>
    <?php echo $mailField->name ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <?php echo $this->BcText->listValue('MailFields.type', $mailField->type) ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $mailField->group_field ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo $this->BcText->booleanMark($mailField->not_empty) ?></td>
  <?php echo $this->BcListTable->dispatchShowRow($mailField) ?>
  <td class="bca-table-listup__tbody-td">
    <?php echo $this->BcTime->format($mailField->created, 'yyyy-MM-dd') ?><br>
    <?php echo $this->BcTime->format($mailField->modified, 'yyyy-MM-dd') ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if ($mailField->use_field): ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'unpublish', $mailContent->id, $mailField->id], [
      'title' => __d('baser', '非公開'),
      'class' => 'btn-unpublish bca-btn-icon',
      'data-bca-btn-type' => 'unpublish',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?php else: ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'publish', $mailContent->id, $mailField->id], [
      'title' => __d('baser', '公開'),
      'class' => 'btn-publish bca-btn-icon',
      'data-bca-btn-type' => 'publish',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?php endif ?>
    <?php $this->BcBaser->link('', ['action' => 'edit', $mailContent->id, $mailField->id], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'copy', $mailContent->id, $mailField->id], [
      'title' => __d('baser', 'コピー'),
      'class' => 'btn-copy bca-icon--copy bca-btn-icon',
      'data-bca-btn-type' => 'copy',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'delete', $mailContent->id, $mailField->id], [
      'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $mailField->name),
      'title' => __d('baser', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </td>
</tr>
