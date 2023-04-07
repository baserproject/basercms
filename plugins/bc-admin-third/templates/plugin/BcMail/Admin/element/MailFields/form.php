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
 * @var \BcMail\View\MailAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcAdminForm->hidden('mail_content_id') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', '基本項目') ?></h2>

<section class="bca-section" data-bca-section-type='form-group'>
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($this->getRequest()->getParam('action') === 'admin_edit'): ?>
      <tr>
        <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('no', 'No') ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo h($this->BcAdminForm->getSourceValue('no')) ?>
          <?php echo $this->BcAdminForm->control('no', ['type' => 'hidden']) ?>
        </td>
      </tr>
    <?php endif; ?>
    <tr id="RowFieldName">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('field_name', __d('baser_core', 'フィールド名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('field_name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('field_name') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', '重複しない半角英数字で入力してください。') ?></div>
      </td>
    </tr>
    <tr id="RowName">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser_core', '項目名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('name') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', '項目を特定しやすいわかりやすい名前を入力してください。日本語可。') ?></div>
      </td>
    </tr>
    <tr id="RowType">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('type', __d('baser_core', 'タイプ')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('type', ['type' => 'select', 'options' => $this->BcAdminForm->getControlSource('MailFields.type')]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('type') ?>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', 'Eメールを選択すると、メールフォーム送信の際、入力されたEメール宛に自動返信メールを送信します。') ?><br/>
              <small>※ 前バージョンとの互換性のため、フィールド名を「email_1」とした場合、Eメールを選択しなくても自動返信メールを送信します。</small></li>
            <li><?php echo __d('baser_core', '自動補完郵便番号の場合は、選択リストに都道府県のフィールドと住所のフィールドのリストを指定します。') ?></li>
          </ul>
        </div>
      </td>
    </tr>
    <tr id="RowHead">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('head', __d('baser_core', '項目見出し')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('head', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('head') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', ' グループとして設定する場合、同グループの２番目以降のフィールドについてこの項目の入力は不要です。 ') ?></div>
      </td>
    </tr>
    <tr id="RowNotEmpty">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('not_empty', __d('baser_core', '入力必須')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('valid', ['type' => 'checkbox', 'label' => __d('baser_core', '入力必須とする')]) ?>
        <?php echo $this->BcAdminForm->error('valid') ?>
        <?php echo $this->BcAdminForm->control('not_empty', ['type' => 'checkbox', 'label' => __d('baser_core', '項目見出しに必須マークを表示する')]) ?>
        <?php echo $this->BcAdminForm->error('not_empty') ?>
      </td>
    </tr>
    <tr id="RowValidEx">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('valid_ex', __d('baser_core', '入力チェック')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('valid_ex', ['type' => 'multiCheckbox', 'options' => $this->BcAdminForm->getControlSource('MailFields.valid_ex')]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('valid_ex') ?>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', 'Eメール比較チェック：利用するには「Eメール」タイプのフィールドを二つ作成し、グループ入力チェックに任意の同じ値を入力します。') ?></li>
            <li><?php echo __d('baser_core', 'グループチェック：グループで連帯して入力チェックを行うには同じグループ名を入力します。') ?></li>
            <li><?php echo __d('baser_core', '日付チェック：日付形式かどうかのチェックです。') ?></li>
            <li><?php echo __d('baser_core', 'ファイルアップロードサイズ制限：利用するには、「ファイル」タイプを選択し、オプション項目に、上限となるサイズを次の形式のように | 区切りで入力します。「maxFileSize|10（単位：MB）」') ?></li>
            <li><?php echo __d('baser_core', 'ファイル拡張子チェック：利用するには、「ファイル」タイプを選択し、オプション項目に、アップロードを許可する拡張子を次の形式のように | 区切りで入力します。「fileExt|jpg,pdf」') ?></li>
            <li><?php echo __d('baser_core', '正規表現チェック：利用するには、オプション項目に、正規表現を次の形式のように | 区切りで入力します。「regex|\d+」 入力した正規表現は以下の書式で実行されます。 /\A○○○\z/us') ?></li>
          </ul>
        </div>
      </td>
    </tr>
    <tr id="RowAttention">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('attention', __d('baser_core', '注意書き')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('attention', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
        <?php echo $this->BcAdminForm->error('attention') ?>
      </td>
    </tr>
    <tr id="RowBeforeAttachment">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('before_attachment', __d('baser_core', '前見出し')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('before_attachment', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
        <?php echo $this->BcAdminForm->error('before_attachment') ?>
      </td>
    </tr>
    <tr id="RowAfterAttachment">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('after_attachment', __d('baser_core', '後見出し')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('after_attachment', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
        <?php echo $this->BcAdminForm->error('after_attachment') ?>
      </td>
    </tr>
    <tr id="RowDescription">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('description', __d('baser_core', '説明文')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('description', ['type' => 'textarea', 'cols' => 35, 'rows' => 3]) ?>
        <?php echo $this->BcAdminForm->error('description') ?>
      </td>
    </tr>
    <tr id="RowSource">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('source', __d('baser_core', '選択リスト')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('source', ['type' => 'textarea', 'cols' => 35, 'rows' => 4]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('source') ?>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', 'ラジオボタン、セレクトボックス、マルチチェックボックスの場合の選択リスト指定します。') ?></li>
            <li><?php echo __d('baser_core', '自動補完郵便番号の場合は、都道府県のフィールドと住所のフィールドのリストを指定します。') ?></li>
            <li><?php echo __d('baser_core', 'リストは　|　で区切って入力します。') ?></li>
          </ul>
        </div>
      </td>
    </tr>
    <tr id="RowSize">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('size', __d('baser_core', '表示サイズ')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('size', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
        <?php echo $this->BcAdminForm->error('size') ?>
      </td>
    </tr>
    <tr id="RowRows">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('text_rows', __d('baser_core', '行数')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('text_rows', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <?php echo $this->BcAdminForm->error('text_rows') ?>
        <div class="bca-helptext"><?php echo __d('baser_core', 'テキストボックスの場合の行数を指定します。') ?></div>
      </td>
    </tr>
    <tr id="RowMaxlength">
      <th
        class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('maxlength', __d('baser_core', '最大値')) ?></th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('maxlength', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
        &nbsp;<?php echo __d('baser_core', '文字') ?>
        <?php echo $this->BcAdminForm->error('maxlength') ?>
      </td>
    </tr>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</section>

<section class="bca-section" data-bca-section-type="form-group">
  <div class="bca-collapse__action">
    <button
      type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#mailFieldSettingBody"
      aria-expanded="false"
      aria-controls="mailFieldSettingBody">
        <?php echo __d('baser_core', '詳細設定') ?>&nbsp;&nbsp;
        <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="mailFieldSettingBody" data-bca-state="">
    <table class="form-table bca-form-table" id="formOptionBody">
      <tr id="RowGroupField">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('group_field', __d('baser_core', 'グループ名')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('group_field', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('group_field') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', '各項目を同じグループとするには同じグループ名を入力します。') ?></li>
              <li><?php echo __d('baser_core', '半角英数字で入力してください。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr id="RowGroupValid">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('group_valid', __d('baser_core', 'グループ入力チェック')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('group_valid', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('group_valid') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'グループで連帯して入力チェックを行うには同じグループ名を入力します。') ?></li>
              <li><?php echo __d('baser_core', 'グループ内の項目が一つでもエラーとなるとグループ内の全ての項目にエラーを意味する背景色が付きます。') ?></li>
              <li><?php echo __d('baser_core', '半角英数字で入力してください。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr id="RowOptions">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('options', __d('baser_core', 'オプション')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('options', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
          <?php echo $this->BcAdminForm->error('options') ?>
        </td>
      </tr>
      <tr id="RowAutoComplete">
        <th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('autocomplete', __d('baser_core', 'オートコンプリート')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('autocomplete', ['type' => 'select', 'options' => $autoCompleteOptions]) ?>
          <?php echo $this->BcAdminForm->error('autocomplete') ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'オートコンプリート属性については<a href="https://developer.mozilla.org/ja/docs/Web/HTML/Attributes/autocomplete" target="_blank">リファレンス</a>を参照してください。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr id="RowClass">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('class', __d('baser_core', 'クラス名')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('class', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
          <?php echo $this->BcAdminForm->error('class') ?>
        </td>
      </tr>
      <tr id="RowSeparator">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('delimiter', __d('baser_core', '区切り文字')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('delimiter', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', '空白の場合は自動で「＆nbsp;＆nbsp;」が挿入されます') ?></li>
              <li><?php echo __d('baser_core', '空にしたいときは半角スペースを入力してください。') ?></li>
            </ul>
          </div>
          <?php echo $this->BcAdminForm->error('delimiter') ?>
        </td>
      </tr>
      <tr id="RowDefault">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('default_value', __d('baser_core', '初期値')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('default_value', ['type' => 'textarea', 'cols' => 35, 'rows' => 2]) ?>
          <?php echo $this->BcAdminForm->error('default_value') ?>
        </td>
      </tr>
      <tr id="RowAutoConvert">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('auto_convert', __d('baser_core', '自動変換')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('auto_convert', ['type' => 'select', 'options' => $this->BcAdminForm->getControlSource('MailFields.auto_convert'), 'empty' => __d('baser_core', 'なし')]) ?>
          <?php echo $this->BcAdminForm->error('auto_convert') ?>
        </td>
      </tr>
      <tr id="RowUseField">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('use_field', __d('baser_core', '利用状態')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('use_field', ['type' => 'checkbox', 'label' => __d('baser_core', '利用中')]) ?>
          <?php echo $this->BcAdminForm->error('use_field') ?>
        </td>
      </tr>
      <tr id="RowNoSend">
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('no_send', __d('baser_core', 'メール送信')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('no_send', ['type' => 'radio', 'options' => [__d('baser_core', '送信する'), __d('baser_core', '送信しない')]]) ?>
          <?php echo $this->BcAdminForm->error('no_send') ?>
        </td>
      </tr>
      <?php echo $this->BcAdminForm->dispatchAfterForm('option') ?>
    </table>
  </div>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>
