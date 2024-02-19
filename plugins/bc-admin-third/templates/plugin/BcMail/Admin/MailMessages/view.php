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
 * [ADMIN] 受信メール詳細
 * @var \BcMail\View\MailAdminAppView $this
 * @var \BcMail\Model\Entity\MailMessage $mailMessage
 * @var \Cake\ORM\ResultSet $mailFields
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core',
  '{0}｜受信メール詳細',
  $this->getRequest()->getAttribute('currentContent')->title
));
?>


<table class="list-table bca-form-table" id="ListTable">
  <tr>
    <th class="col-head bca-form-table__label">No</th>
    <td class="col-input bca-form-table__input"><?php echo $mailMessage->id ?></td>
  </tr>
  <tr>
    <th class="col-head bca-form-table__label"><?php echo __d('baser_core', '受信日時') ?></th>
    <td class="col-input bca-form-table__input"><?php echo $this->BcTime->format($mailMessage->created) ?></td>
  </tr>
  <?php
  $groupField = null;
  foreach($mailFields as $key => $field) {
    if ($field->use_field) {
      $nextKey = $key + 1;
      /* 項目名 */
      if ($groupField != $field->group_field || (!$groupField && !$field->group_field)) {
        echo '<tr>';
        echo '<th class="col-head bca-form-table__label">' . $field->head . '</th>';
        echo '<td class="col-input bca-form-table__input">';
      }
      if (!empty($mailMessage->{$field->field_name})) {
        echo $field->before_attachment;
      }
      if (!$field->no_send) {
        if ($field->type == 'file') {
          echo $this->Maildata->control(
            $field->type,
            $mailMessage->{$field->field_name},
            $this->Mailfield->getOptions($field)
          );
        } else {
          $body = $this->Maildata->control(
            $field->type,
            $mailMessage->{$field->field_name},
            $this->Mailfield->getOptions($field)
          );
          if ($body) {
            $body = $this->BcText->autoLink($body, ['target' => '_blank']);
          }
          echo nl2br($body);
        }
      }
      if (!empty($mailMessage->{$field->field_name})) {
        echo $field->after_attachment;
      }
      echo '&nbsp;';
      if (($this->BcArray->last($mailFields, $key)) ||
        ($field->group_field != $mailFields[$nextKey]->group_field) ||
        (!$field->group_field && !$mailFields[$nextKey]->group_field) ||
        ($field->group_field != $mailFields[$nextKey]->group_field && $this->BcArray->first($mailFields, $key))) {
        echo '</td></tr>';
      }
      $groupField = $field->group_field;
    }
  }
  ?>
</table>

<!-- button -->
<div class="bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
      'plugin' => 'BcMail',
      'controller' => 'MailMessages',
      'action' => 'index',
      $mailContent->id
    ], [
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '削除'),
      ['action' => 'delete', $mailContent->id, $mailMessage->id], [
        'confirm' => __d('baser_core', '受信メール No「{0}」を削除してもいいですか？', $mailMessage->id),
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm',
        'data-bca-btn-color' => 'danger'
      ]) ?>
  </div>
</div>
