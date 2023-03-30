<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールフィールド 一覧　行
 * @var \BcMail\View\MailAdminAppView $this
 * @var int $count
 * @var \BcMail\Model\Entity\MailContent $mailContent
 * @var \BcMail\Model\Entity\MailMessage $mailMessage
 * @var \Cake\ORM\ResultSet $mailFields
 */
?>


<tr id="Row<?php echo $count + 1 ?>">
  <td class="row-tools bca-table-listup__tbody-td">
    <?php echo $this->BcAdminForm->control('batch_targets.' . $mailMessage->id, [
      'type' => 'checkbox',
      'label' => '<span class="bca-visually-hidden">チェックする</span>',
      'class' => 'batch-targets bca-checkbox__input',
      'value' => $mailMessage->id,
      'escape' => false
    ]) ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td"><?php echo $mailMessage->id ?></td>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php echo date('Y/m/d H:i', strtotime($mailMessage->created)); ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php
    $inData = [];
    $fileExists = false;
    ?>
    <?php foreach($mailFields as $mailField): ?>
      <?php if (!$mailField->no_send && $mailField->use_field): ?>
        <?php
        if ($mailField->type !== 'file') {
          $inData[] = h($this->Maildata->control(
            $mailField->type,
            $mailMessage[$mailField->field_name],
            $this->Mailfield->getOptions($mailMessage)
          ));
        } else {
          if (!empty($mailMessage[$mailField->field_name])) {
            $fileExists = true;
          }
        }
        ?>
      <?php endif ?>
    <?php endforeach ?>
    <?php echo $this->Text->truncate(implode(',', $inData), 170) ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($fileExists): ?>
      ○
    <?php endif ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php $this->BcBaser->link('', ['action' => 'view', $mailContent->id, $mailMessage->id], [
      'title' => __d('baser_core', '詳細'),
      'class' => 'btn-view bca-btn-icon',
      'data-bca-btn-type' => 'preview',
      'data-bca-btn-size' => 'lg'
    ]) ?>
    <?php echo $this->BcAdminForm->postLink('', ['action' => 'delete', $mailContent->id, $mailMessage->id], [
      'confirm' => __d('baser_core', "No.{0} を本当に削除してもいいですか？", $mailMessage->id),
      'title' => __d('baser_core', '削除'),
      'class' => 'btn-delete bca-btn-icon',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'lg'
    ]) ?>
  </td>
  <?php echo $this->BcListTable->dispatchShowRow($mailMessage) ?>
</tr>
