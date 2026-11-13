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
 * カスタムフィールド / フォーム
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 */
?>



<tr id="RowMetaBcCcBurgerEditor" class="bca-row-meta">
  <th class="col-head bca-form-table__label">
    <?php echo $this->BcAdminForm->label('', __d('baser_core', 'BurgerEditor設定')) ?>
  </th>
  <td class="col-input bca-form-table__input">
    <?php echo $this->BcAdminForm->control('meta.BcCcBurgerEditor.editor_use_draft', ['type' => 'radio', 'options' => [
      true => __d('baser_core', '下書きを利用する'),
      false => __d('baser_core', '下書きを利用しない')
    ]]) ?>
    <i class="bca-icon--question-circle bca-help"></i>　
    <div class="bca-helptext">
      <?php echo __d('baser_core', '下書きを利用する場合は、別途、下書き保存用のフィールドとして現在のフィールド名の後ろに「_draft」を付与したフィールドを作成します。') ?>
    </div>
    <?php echo $this->BcAdminForm->error('meta.BcCcBurgerEditor.editor_use_draft') ?>
  </td>
</tr>
