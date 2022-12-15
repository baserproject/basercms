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
 * [ADMIN] 受信メール一覧
 * @var \BcMail\View\MailAdminAppView $this
 * @var \BcMail\Model\Entity\MailContent $mailContent
 */
$this->BcAdmin->setTitle(__d(
  'baser',
  '{0}｜受信メール一覧',
  $this->getRequest()->getAttribute('currentContent')->title
));
$this->BcAdmin->setHelp('mail_messages_index');
?>

<div class="panel-box bca-panel-box" id="FunctionBox">
  <?php echo $this->BcAdminForm->create(null, [
    'type' => 'get',
    'url' => ['controller' => 'MailFields', 'action' => 'download_csv', $mailContent->id]
  ]) ?>
  <?php echo $this->BcAdminForm->control('encoding', [
    'type' => 'radio',
    'options' => ['UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'],
    'value' => 'UTF-8'
  ]) ?>
  &nbsp;&nbsp;
  <?php echo $this->BcAdminForm->submit(__d('baser', 'CSVダウンロード'), ['div' => false, 'class' => 'bca-btn']) ?>
  <?php echo $this->BcAdminForm->end() ?>
</div>

<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element('MailMessages/index_list') ?>
</div>
