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

?>



<tr id="RowMetaCuCcBurgerEditor" class="bca-row-meta">
  <th class="col-head bca-form-table__label">
    <?php echo $this->BcAdminForm->label('', __d('baser_core', 'burgerEditor 下書き')) ?>
  </th>
  <td class="col-input bca-form-table__input">
    <?php echo $this->BcAdminForm->control('meta.CuCcBurgerEditor.editor_use_draft', ['type' => 'radio', 'options' => [
    true => __d('baser_core', '利用する'),
    false => __d('baser_core', '利用しない')
    ]]) ?>

    <?php echo $this->BcAdminForm->error('meta.CuCcBurgerEditor.editor_use_draft') ?>
  </td>
</tr>
