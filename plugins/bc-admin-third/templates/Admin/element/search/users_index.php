<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright    Copyright (c) baserCMS Users Community
 * @link      https://basercms.net baserCMS Project
 * @since      baserCMS v 2.0.0
 * @license      https://basercms.net/license/index.html
 */

use BaserCore\View\BcAdminAppView;

/**
 * User Index Search
 * @var BcAdminAppView $this
 * @checked
 * @unitTest
 * @noTodo
 */
$userGroupList = $this->BcAdminForm->getControlSource('Users.user_group_id');
?>


<?php echo $this->BcAdminForm->create(null, ['novalidate' => true, 'type' => 'get', 'url' => ['action' => 'index']]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('user_group_id', __d('baser', 'ユーザーグループ'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('user_group_id', ['type' => 'select', 'options' => $userGroupList, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
</p>
<div class="button bca-search__btns">
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn', 'data-bca-btn-type' => 'search']) ?>
  </div>
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser', 'クリア'), ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?>
  </div>
</div>
<?php echo $this->Form->end() ?>
