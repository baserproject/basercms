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
 * @noTodo
 * @unitTest
 */
?>


<div id="AddBlogCategoryForm" title="<?php echo __d('baser_core', 'カテゴリ新規追加') ?>" style="display:none">
  <dl>
    <dt><?php echo $this->BcAdminForm->label('BlogCategory.title', __d('baser_core', 'カテゴリタイトル')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('BlogCategory.title', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '', 'autofocus' => true]) ?>
    </dd>
    <dt><?php echo $this->BcAdminForm->label('BlogCategory.name', __d('baser_core', 'カテゴリ名')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('BlogCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '']) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div class="bca-helptext">
        <ul>
          <li><?php echo __d('baser_core', 'URLに利用されます') ?></li>
          <li><?php echo __d('baser_core', '半角のみで入力してください') ?></li>
          <li><?php echo __d('baser_core', '空の場合はカテゴリタイトルから値が自動で設定されます') ?></li>
        </ul>
      </div>
    </dd>
  </dl>
</div>
