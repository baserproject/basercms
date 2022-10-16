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
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcAdmin->setTitle(__d('baser', 'ゴミ箱'));
$this->BcBaser->element('Contents/index_setup_tree');
?>


<?php echo $this->BcAdminForm->control('ViewSetting.mode', ['type' => 'hidden', 'value' => 'trash']) ?>

<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>

<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element("Contents/index_trash"); ?>
</div>
