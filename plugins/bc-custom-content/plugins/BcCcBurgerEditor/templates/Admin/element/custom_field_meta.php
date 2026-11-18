<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
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
      <?php echo __d('baser_core', '下書きを利用する場合は、別途、下書き保存用のフィールドを次の手順で作成してください。') ?>
      <ol>
        <li><?php echo __d('baser_core', 'フィールド名を現在のフィールド名の後ろに「_draft」を付与した名称、フィールドタイプを「テキストエリア」として、新しいフィールドを作成します。') ?></li>
        <li><?php echo __d('baser_core', 'テーブル編集画面で、作成した下書き用フィールドをテーブルに登録します。') ?></li>
        <li><?php echo __d('baser_core', '一度保存した後、下書き用フィールドの詳細編集ボタンを押し、利用状況「利用する」のチェックを外します。') ?></li>
      </ol>
    </div>
    <?php echo $this->BcAdminForm->error('meta.BcCcBurgerEditor.editor_use_draft') ?>
  </td>
</tr>
