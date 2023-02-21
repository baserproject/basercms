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


<tr id="RowMetaBcCcRelated" class="bca-row-meta">
    <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('', __d('baser', '関連データ設定')) ?>
    </th>
    <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->label('meta.BcCcRelated.custom_table_id', __d('baser', 'カスタムテーブル')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcRelated.custom_table_id', ['type' => 'select', 'options' => $this->BcCcRelated->getTableList()]) ?>　　
        <i class="bca-icon--question-circle bca-help"></i>　　
        <div class="bca-helptext">
          <?php echo __d('baser', 'データ元となるカスタムテーブルを指定します。') ?>
        </div>
        <br>
        <?php echo $this->BcAdminForm->label('meta.BcCcRelated.filter_name', __d('baser', '絞り込みフィールド')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcRelated.filter_name', ['type' => 'text', 'size' => 20]) ?>
        &nbsp;&nbsp;
        <?php echo $this->BcAdminForm->label('meta.BcCcRelated.filter_value', __d('baser', '絞り込み値')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcRelated.filter_value', ['type' => 'text', 'size' => 20]) ?>
        <i class="bca-icon--question-circle bca-help"></i>　　
        <div class="bca-helptext">
          <?php echo __d('baser', 'データの絞り込みを行う場合、絞り込みのフィールド名と値を設定します。') ?>
        </div>

        <?php echo $this->BcAdminForm->error('meta.BcCcRelated.custom_table_id') ?>
        <?php echo $this->BcAdminForm->error('meta.BcCcRelated.filter_name') ?>
        <?php echo $this->BcAdminForm->error('meta.BcCcRelated.filter_value') ?>
    </td>
</tr>
