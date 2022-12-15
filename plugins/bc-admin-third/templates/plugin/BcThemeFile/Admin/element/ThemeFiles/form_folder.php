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
 * [ADMIN] テーマフォルダ登録・編集
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $theme
 * @var bool $isWritable
 * @checked
 * @unitTest
 * @noTodo
 */
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php if ($theme != 'core' && !$isWritable): ?>
  <div id="AlertMessage">ファイルに書き込み権限がないので編集できません。</div>
<?php endif ?>

<?php echo $this->BcAdminForm->control('parent', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('fullpath', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', 'フォルダ名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($this->getRequest()->getParam('action') != 'view_folder'): ?>
          <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser', 'フォルダ名は半角で入力してください。') ?></li>
            </ul>
          </div>
          <?php echo $this->BcAdminForm->error('name') ?>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'readonly' => 'readonly']) ?>
        <?php endif ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>
