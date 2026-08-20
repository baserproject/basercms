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
 * [ADMIN] ブログカテゴリ 一覧 表示切替
 * @var BcBlog\View\BlogAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$listType = $this->request->getQuery('list_type') ?: 2;
?>


<div class="panel-box bca-panel-box" id="ViewSetting">
  <div class="bca-panel-box__inline-fields">
    <div class="bca-panel-box__inline-fields-item">
      <label class="bca-panel-box__inline-fields-title"><?php echo __d('baser_core', '表示') ?></label>
      <?php echo $this->BcAdminForm->control('ViewSetting.list_type', [
        'type' => 'radio',
        'options' => [
          1 => __d('baser_core', 'ツリー形式'),
          2 => __d('baser_core', '表形式')
        ],
        'value' => $listType,
        'hiddenField' => false
      ]) ?>
    </div>
    <div class="bca-panel-box__inline-fields-separator"></div>
    <div id="GrpChangeTreeOpenClose" style="display:none">
      <button type="button" id="BtnOpenTree" class="bca-btn"><?php echo __d('baser_core', '全て展開') ?></button>
      <button type="button" id="BtnCloseTree" class="bca-btn"><?php echo __d('baser_core', '全て閉じる') ?></button>
    </div>
  </div>
</div>
