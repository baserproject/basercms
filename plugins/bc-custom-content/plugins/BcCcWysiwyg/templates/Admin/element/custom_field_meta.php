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


<tr id="RowMetaBcCcWysiwyg" class="bca-row-meta">
    <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('', __d('baser', 'Wysiwyg エディタ設定')) ?>
    </th>
    <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->label('meta.BcCcWysiwyg.width', __d('baser', '横幅')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcWysiwyg.width', ['type' => 'text', 'size' => 10, 'placeholder' => '100%']) ?>　　
        <?php echo $this->BcAdminForm->label('meta.BcCcWysiwyg.height', __d('baser', '高さ')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcWysiwyg.height', ['type' => 'text', 'size' => 10, 'placeholder' => '200px']) ?>
        <i class="bca-icon--question-circle bca-help"></i>　　
        <div class="bca-helptext">
          <?php echo __d('baser', 'ピクセル（px）、または、パーセンテージ（%）で単位も含めて入力してください。') ?>
        </div>
        <?php echo $this->BcAdminForm->label('meta.BcCcWysiwyg.editor_tool_type', __d('baser', 'エディタのタイプ')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCcWysiwyg.editor_tool_type', ['type' => 'select', 'options' => [
            'simple' => __d('baser', 'シンプル'),
            'normal' => __d('baser', 'ノーマル')
        ]]) ?>
        <?php echo $this->BcAdminForm->error('meta.BcCcWysiwyg.width') ?>
        <?php echo $this->BcAdminForm->error('meta.BcCcWysiwyg.height') ?>
        <?php echo $this->BcAdminForm->error('meta.BcCcWysiwyg.editor_tool_type') ?>
    </td>
</tr>
