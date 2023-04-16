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
 * @var \BcCustomContent\Model\Entity\CustomField $entity
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr id="RowMetaBcCcCheckbox" class="bca-row-meta">
    <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('', __d('baser_core', 'チェックボックス設定')) ?>
    </th>
    <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->label('meta.BcCcCheckbox.label', __d('baser_core', 'ラベル')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcCheckbox.label', [
            'type' => 'text',
            'size' => 60,
            'v-model' => 'checkboxLabel'
        ]) ?>　　
        <?php echo $this->BcAdminForm->error('meta.BcCcCheckbox.label') ?>
    </td>
</tr>
