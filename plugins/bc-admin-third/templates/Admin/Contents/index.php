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
 * [ADMIN] 統合コンテンツ一覧
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $template
 * @checked
 * @unitTest
 * @noTodo
 */

$this->BcAdmin->setTitle(__d('baser_core', 'コンテンツ一覧'));
$this->BcAdmin->setSearch('contents_index');
$this->BcAdmin->setHelp('contents_index');
$this->BcBaser->element('Contents/index_setup_tree');
?>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>

<?php $this->BcBaser->element('Contents/index_view_setting') ?>

<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element("Contents/{$template}"); ?>
</div>
