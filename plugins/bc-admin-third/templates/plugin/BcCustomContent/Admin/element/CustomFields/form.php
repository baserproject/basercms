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
$a = $entity->toArray();
$fieldTypes = \Cake\Core\Configure::read('BcCustomContent.fieldTypes');
$this->BcBaser->js('BcCustomContent.admin/custom_fields/form.bundle', false, [
  'id' => 'AdminCustomFieldsFormScript',
  'defer' => true,
  'data-setting' => json_encode($fieldTypes),
  'data-entity' => json_encode($entity)
]);
?>


<div id="AdminCustomFieldsForm">

<?php echo $this->BcFormTable->dispatchBefore() ?>

<section class="bca-section" data-bca-section-type="form-group">
  <table class="form-table bca-form-table" data-bca-table-type="type2">
    <?php if ($this->getRequest()->getParam('action') === 'edit'): ?>
      <tr>
        <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo h($entity->id) ?>
        </td>
      </tr>
    <?php endif ?>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', 'フィールド名')) ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', [
          'type' => 'text',
          'size' => 40,
          'maxlength' => 255,
          'v-model' => 'entity.name'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '半角英数字とアンダースコア（ _ ）のみ利用可能です。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('title', __d('baser', 'タイトル')) ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', [
          'type' => 'text',
          'size' => 40,
          'maxlength' => 255,
          'v-model' => 'entity.title'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'フィールドの内容が分かりやすいタイトルを登録します。日本語が利用できます。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('title') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('type', __d('baser', 'タイプ')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('type', [
          'type' => 'select',
          'options' => $this->BcAdminForm->getControlSource('BcCustomContent.CustomFields.field_type'),
          'v-model' => 'entity.type',
          '@change' => 'initByType'
        ]) ?>
        <?php echo $this->BcAdminForm->error('type') ?>
      </td>
    </tr>

    <?php $this->CustomContentAdmin->displayPluginMeta() ?>

    <tr v-show="showRowCheck">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('', __d('baser', '入力チェック')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('validate', [
          'type' => 'multiCheckbox',
          'options' => $this->BcAdminForm->getControlSource('BcCustomContent.CustomFields.validate'),
          'v-model' => 'entity.validate',
          '@change' => 'initValidateOptionControls'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <ul>
            <li>
              <strong><?php echo __d('baser', 'Eメール形式、数値、半角英数、全角カタカナ、全角ひらがな、日付チェック') ?></strong><br>
              <?php echo __d('baser', '入力した文字列の形式をチェックします。') ?>
            </li>
            <li>
              <strong><?php echo __d('baser', 'Eメール比較チェック') ?></strong><br>
              <?php echo __d('baser', '二つの「Eメール」タイプのフィールドを作成し、両方の内容が同じかどうかをチェックします。利用するには、「Eメール比較先フィールド名」に対象となる関連フィールド名を入力します。') ?>
            </li>
            <li>
              <strong><?php echo __d('baser', 'ファイルアップロードサイズ制限') ?></strong><br>
              <?php echo __d('baser', '利用するには、「ファイルアップロードサイズ上限」を入力します。') ?>
            </li>
            <li>
              <strong><?php echo __d('baser', 'ファイル拡張子チェック') ?></strong><br>
              <?php echo __d('baser', '利用するには、「アップロードを許可する拡張子」に拡張子をカンマ区切りで入力します」') ?>
            </li>
          </ul>
        </div>

        <span v-show="showControlEmailConfirm" style="display: block">
          <?php echo $this->BcAdminForm->label('meta.BcCustomContent.email_confirm', __d('baser', 'Eメール比較先フィールド名')) ?>&nbsp;
          <?php echo $this->BcAdminForm->control('meta.BcCustomContent.email_confirm', [
            'type' => 'text',
            'size' => 20,
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            Eメール比較チェックの対象となる、フィールド名を入力します。<br>
            利用しているテーブルに紐づく関連フィールドのフィールド名となりますので注意が必要です。
          </div>
        </span>

        <span style="display: block">
        <span v-show="showControlMaxFileSize" style="white-space: nowrap">
        <?php echo $this->BcAdminForm->label('meta.BcCustomContent.max_file_size', __d('baser', 'ファイルアップロードサイズ上限')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCustomContent.max_file_size', [
          'type' => 'text',
          'size' => 5,
          'placeholder' => '20',
        ]) ?> MB　
        </span>

        <span v-show="showControlFileExt" style="white-space: nowrap">
        <?php echo $this->BcAdminForm->label('meta.BcCustomContent.file_ext', __d('baser', 'アップロードを許可する拡張子')) ?>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('meta.BcCustomContent.file_ext', [
          'type' => 'text',
          'size' => 20,
          'placeholder' => 'jpg,pdf',
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>　　
        <div class="bca-helptext">
          <?php echo __d('baser', '拡張子を次の形式のようにカンマ（,）区切りで入力します。「jpg,pdf」') ?>
        </div>
        </span>
        </span>

        <?php echo $this->BcAdminForm->error('validate') ?>
      </td>
    </tr>

    <tr v-show="showRowRegex">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('regex', __d('baser', '正規表現入力チェック')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->label('regex', __d('baser', '正規表現')) ?>&nbsp;
        <?php echo $this->BcAdminForm->control('regex', [
          'type' => 'text',
          'size' => 60,
          'placeholder' => '^[0-9]+$',
          'v-model' => 'entity.regex'
        ]) ?><br>
        <?php echo $this->BcAdminForm->label('regex_error_message', __d('baser', 'エラーメッセージ')) ?>&nbsp;
        <?php echo $this->BcAdminForm->control('regex_error_message', [
          'type' => 'text',
          'size' => 60,
          'placeholder' => '正規表現チェックでのエラーメッセージ',
          'v-model' => 'entity.regex_error_message'
        ]) ?>&nbsp;
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '正規表現チェックを利用するには、入力欄に正規表現を入力します。スラッシュで囲う必要はありません。入力した正規表現は以下の書式で実行されます。 /\A○○○\z/us') ?>
        </div>
        <?php echo $this->BcAdminForm->error('regex') ?>
        <?php echo $this->BcAdminForm->error('regex_error_message') ?>
      </td>
    </tr>

    <tr v-show="showRowText">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('', __d('baser', 'テキスト関連設定')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <span v-show="showControlSize">
        <?php echo $this->BcAdminForm->label('size', __d('baser', '横幅サイズ')) ?>&nbsp;
        <?php echo $this->BcAdminForm->control('size', [
          'type' => 'text',
          'size' => 5,
          'v-model' => 'entity.size'
        ]) ?>&nbsp;&nbsp;
        </span>
        <span v-show="showControlLine">
        <?php echo $this->BcAdminForm->label('line', __d('baser', '行数')) ?>&nbsp;
        <?php echo $this->BcAdminForm->control('line', [
          'type' => 'text',
          'size' => 5,
          'v-model' => 'entity.line'
        ]) ?>&nbsp;&nbsp;
        </span>
        <span v-show="showControlMaxLength">
        <?php echo $this->BcAdminForm->label('max_length', __d('baser', '最大文字数')) ?>&nbsp;
        <?php echo $this->BcAdminForm->control('max_length', [
          'type' => 'text',
          'size' => 5,
          'v-model' => 'entity.max_length'
        ]) ?>&nbsp;&nbsp;
        </span>
        <span v-show="showControlAutoConvert">
        <?php echo $this->BcAdminForm->label('auto_convert', __d('baser', '自動変換')) ?>&nbsp;
        <?php echo $this->BcAdminForm->control('auto_convert', [
          'type' => 'select',
          'options' => $this->BcAdminForm->getControlSource('BcCustomContent.CustomFields.auto_convert'),
          'empty' => __d('baser', 'なし'),
          'v-model' => 'entity.auto_convert'
        ]) ?>&nbsp;&nbsp;
        </span>
        <span v-show="showControlCounter">
        <?php echo $this->BcAdminForm->control('counter', [
          'type' => 'checkbox',
          'label' => __d('baser', '文字数カウンターを表示する'),
          'v-model' => 'entity.counter'
        ]) ?>
        </span>
        </span>
        <?php echo $this->BcAdminForm->error('size') ?>
        <?php echo $this->BcAdminForm->error('line') ?>
        <?php echo $this->BcAdminForm->error('max_length') ?>
        <?php echo $this->BcAdminForm->error('auto_convert') ?>
        <?php echo $this->BcAdminForm->error('counter') ?>
      </td>
    </tr>

    <tr v-show="showRowPlaceholder">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('placeholder', __d('baser', 'プレースホルダー')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('placeholder', [
          'type' => 'textarea',
          'rows' => 2,
          'v-model' => 'entity.placeholder'
        ]) ?>
        <?php echo $this->BcAdminForm->error('placeholder') ?>
      </td>
    </tr>

    <tr v-show="showRowSource">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('source', __d('baser', '選択リスト')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('source', [
          'type' => 'textarea',
          'v-model' => 'entity.source'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '改行で区切って入力してください。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('source') ?>
      </td>
    </tr>

    <tr v-show="showRowDefaultValue">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('default_value', __d('baser', '初期値')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('default_value', [
          'type' => 'textarea',
          'rows' => 2,
          'v-model' => 'entity.default_value'
        ]) ?>
        <?php echo $this->BcAdminForm->error('default_value') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('status', __d('baser', '利用状況')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('status', [
          'type' => 'checkbox',
          'label' => __d('baser', '利用する'),
          'v-model' => 'entity.status'
        ]) ?>
        <?php echo $this->BcAdminForm->error('status') ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div id="CustomFieldPreview">
  <div class="custom-field-preview-inner">
  <span class="preview-icon">PREVIEW</span>
  <?php foreach($fieldTypes as $key => $type): ?>
    <?php if($key === 'group' || $type['controlType'] === 'hidden') continue ?>
    <span v-show="showPreview['<?php echo $key ?>']">
    <?php $this->CustomContentAdmin->BcAdminForm->unlockField('preview.' . $key) ?>
    <?php echo $this->CustomContentAdmin->preview('preview.' . $key, $key, $entity) ?>
    </span>
  <?php endforeach ?>
    <span v-show="showPreview['NonSupport']">プレビュー未対応です</span>
  </div>
</div>

</div>
