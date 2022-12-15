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
 * [ADMIN] メールフィールド 一覧
 * @var \BcMail\View\MailAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜メールフィールド一覧'), $this->getRequest()->getAttribute('currentContent')->title));
$this->BcAdmin->setHelp('mail_fields_index');
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $this->request->getParam('pass.0')],
  'title' => __d('baser', '新規フィールド追加'),
]);
?>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>

<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element('MailFields/index_list') ?>
</div>
