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


<tr id="RowMetaBcCcAutoZip" class="bca-row-meta">
    <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('', __d('baser_core', '自動補完郵便番号設定')) ?>
    </th>
    <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->label('meta.BcCcAutoZip.pref', __d('baser_core', '都道府県フィールド名')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcAutoZip.pref', ['type' => 'text', 'size' => 20]) ?>&nbsp;
        <i class="bca-icon--question-circle bca-help"></i>　　
        <div class="bca-helptext">
          <?php echo __d('baser_core', '自動補完の対象となる都道府県のフィールド名を設定します。利用しているテーブルに紐づく関連フィールドのフィールド名となりますので注意が必要です。') ?>
        </div>
        <?php echo $this->BcAdminForm->label('meta.BcCcAutoZip.address', __d('baser_core', '住所フィールド')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcAutoZip.address', ['type' => 'text', 'size' => 20]) ?>
        <i class="bca-icon--question-circle bca-help"></i>　　
        <div class="bca-helptext">
          <?php echo __d('baser_core', '自動補完の対象となる住所のフィールド名を設定します。利用しているテーブルに紐づく関連フィールドのフィールド名となりますので注意が必要です。') ?>
        </div>

        <?php echo $this->BcAdminForm->error('meta.BcCcRelated.pref') ?>
        <?php echo $this->BcAdminForm->error('meta.BcCcRelated.address') ?>
    </td>
</tr>
