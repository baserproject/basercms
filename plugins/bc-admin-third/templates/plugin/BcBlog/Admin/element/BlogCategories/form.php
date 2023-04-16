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
 * [ADMIN] ブログカテゴリ フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @var array $parents
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @var \BcBlog\Model\Entity\BlogCategory $blogCategory
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcAdminForm->control('blog_content_id', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('status', ['type' => 'hidden']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<!-- form -->
<div class="section">
  <table class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser_core', 'カテゴリ名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', [
          'type' => 'text',
          'size' => 40,
          'maxlength' => 255,
          'autofocus' => true
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('name') ?>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', 'URLに利用されます') ?></li>
            <li><?php echo __d('baser_core', '半角のみで入力してください') ?></li>
          </ul>
        </div>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('title', __d('baser_core', 'カテゴリタイトル')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', [
          'type' => 'text',
          'size' => 40,
          'maxlength' => 255
        ]) ?>
        <?php echo $this->BcAdminForm->error('title') ?>
      </td>
    </tr>
    <?php if ($parents): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('parent_id', __d('baser_core', '親カテゴリ')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php
          echo $this->BcAdminForm->control('parent_id', [
            'type' => 'select',
            'options' => $parents,
            'empty' => __d('baser_core', '指定しない'),
            'escape' => true])
          ?>
          <?php echo $this->BcAdminForm->error('parent_id') ?>
        </td>
      </tr>
    <?php else: ?>
      <?php echo $this->BcAdminForm->control('parent_id', ['type' => 'hidden']) ?>
    <?php endif ?>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>


