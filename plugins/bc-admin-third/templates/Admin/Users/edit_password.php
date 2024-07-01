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

use BaserCore\View\BcAdminAppView;
use BaserCore\Model\Entity\User;

/**
 * Users Edit
 * @var BcAdminAppView $this
 * @var User $user
 */
$this->BcAdmin->setTitle(__d('baser_core', 'パスワード編集'));
?>

<?= $this->BcAdminForm->create($user, ['novalidate' => true]) ?>

<?php // 自動入力を防止する為のダミーフィールド ?>
<input type="password" name="dummy-pass" autocomplete="off" style="top:-100px;left:-100px;position:fixed;">
<?php $this->BcAdminForm->unlockFields('dummy-pass') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('password_1', __d('baser_core', 'パスワード')) ?>
        <?php if ($this->request->getParam('action') == 'add'): ?>
          <span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>&nbsp;
        <?php endif; ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($this->request->getParam('action') == 'edit'): ?><small>
          [<?php echo __d('baser_core', 'パスワードは変更する場合のみ入力してください') ?>]</small><br/><?php endif ?>
        <?php echo $this->BcAdminForm->control('password_1', ['type' => 'password', 'size' => 20, 'maxlength' => 255, 'autocomplete' => 'off']) ?>
        <?php echo $this->BcAdminForm->control('password_2', ['type' => 'password', 'size' => 20, 'maxlength' => 255, 'autocomplete' => 'off', 'placeholder' => __d('baser_core', 'もう一度入力')]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <ul>
            <li>
              <?php if ($this->request->getParam('action') == 'edit'): ?>
                <?php echo __d('baser_core', 'パスワードの変更をする場合は、') ?>
              <?php endif; ?>
              <?php echo __d('baser_core', '確認のため２回入力してください。') ?></li>
            <li><?php echo __d('baser_core', '半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください') ?></li>
          </ul>
        </div>
        <?php echo $this->BcAdminForm->error('password') ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit section bca-actions">
  <div class="bca-actions__main">
    <?= $this->BcAdminForm->button(
      __d('baser_core', '保存'),
      ['div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'id' => 'BtnSave']
    ) ?>
  </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
