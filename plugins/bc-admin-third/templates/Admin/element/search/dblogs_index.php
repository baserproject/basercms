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
 * @var \BaserCore\View\BcAdminAppView
 * @checked
 * @unitTest
 * @noTodo
 */
$userList = $this->BcAdminForm->getControlSource('Users.id');
?>


<?php echo $this->BcAdminForm->create(null, ['novalidate' => true, 'method' => 'get', 'url' => ['action' => 'index']]) ?>
<p class="bca-search__input-list">
  <span class="bca-search__input-item">
    <?php echo $this->BcAdminForm->label('message', __d('baser_core', 'キーワード'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('message', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => '30']) ?>
  </span>
  <span class="bca-search__input-item">
    <?php echo $this->BcAdminForm->label('user_id', __d('baser_core', 'ユーザー'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('user_id', ['type' => 'select', 'options' => $userList, 'empty' => __d('baser_core', '指定なし')]) ?>
  </span>
</p>
<div class="button bca-search__btns">
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
  </div>
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser_core', 'クリア'), ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?>
  </div>
</div>
<?php echo $this->Form->end() ?>
