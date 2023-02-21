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
 * 関連フィールド / フォーム
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomLink $entity
 * @var array $groupFields グループフィールドが対象
 * @checked
 * @noTodo
 * @unitTest
 */
$fieldTypes = \Cake\Core\Configure::read('BcCustomContent.fieldTypes');
$fields = $this->CustomContentAdmin->getFields();
$fieldList = [];
if ($fields->count()) {
  $fieldsArray = $fields->toArray();
  $fieldList = array_combine(\Cake\Utility\Hash::extract($fieldsArray, '{n}.type'), array_values($fieldsArray));
}
$this->BcBaser->i18nScript([
  'confirmMessageOnSaveLink' => __d('baser', 'グループを変更する場合、対象のグループがループ機能を利用している場合に、このフィールドの既存のエントリーデータが失われます。それでも変更してもよろしいですか？'),
]);
?>


<section class="bca-section" data-bca-section-type="form-group">

  <?php echo $this->BcAdminForm->create() ?>

  <?php echo $this->BcAdminForm->control('id', [
    'type' => 'hidden',
    'v-model' => 'link.id'
  ]) ?>
  <?php echo $this->BcAdminForm->control('custom_table_id', [
    'type' => 'hidden',
    'v-model' => 'link.custom_table_id'
  ]) ?>
  <?php echo $this->BcAdminForm->control('custom_field_id', [
    'type' => 'hidden',
    'v-model' => 'link.custom_field_id'
  ]) ?>

  <h2 class="bca-main__header-title">関連フィールド編集</h2>

  <div id="MessageBox" class="message-box">
    <div id="flashMessage" class="message alert-message"></div>
  </div>

  <?php echo $this->BcFormTable->dispatchBefore() ?>

  <table class="form-table bca-form-table" data-bca-table-type="type2">

    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('no', 'No') ?></th>
      <td class="col-input bca-form-table__input">
        {{ link.no }}
        <?php echo $this->BcAdminForm->control('no', [
          'type' => 'hidden',
          'v-model' => 'link.no'
        ]) ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label"><?php echo __d('baser', 'マスターフィールド') ?></th>
      <td class="col-input bca-form-table__input">
        {{ linkFieldTitle }}
        （{{ linkTypeTitle }}）
        <?php $this->BcBaser->link(__d('baser', 'マスタ編集'), '', [
          'class' => 'button-small',
          ':href' => 'editFieldLinkUrl',
          'confirm' => __d('baser', '現在編集中の内容を破棄してカスタムフィールドのマスタ編集画面に移動します。よろしいですか？')
        ]) ?>
      </td>
    </tr>

    <tr v-show="isEnabledParent">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('parent_id', __d('baser', 'グループ')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <span class="bca-select">
          <select name="parent_id" id="parent-id" class="bca-select__select" v-model="link.parent_id">
            <option value="title" selected="selected"><?php echo __d('baser', '指定しない') ?></option>
            <option v-for="(value, key) in parentList" :value="key">{{ value }}</option>
          </select>
        </span>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '他のフィールドと同じグループとして表示する場合にグループを選択します。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('parent_id') ?>
      </td>
    </tr>

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
          'v-model' => 'link.name'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '半角英数字とアンダースコア（ _ ）のみ利用可能です。') ?>
        </div>
        <div class="error-message error-name"></div>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('title', __d('baser', '項目見出し')) ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', [
          'type' => 'text',
          'size' => 40,
          'maxlength' => 255,
          'v-model' => 'link.title'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '項目の内容が分かりやすい見出しを登録します。日本語が利用できます。') ?>
        </div>
        <div class="error-message error-title"></div>
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
          'v-model' => 'link.status'
        ]) ?>
        <?php echo $this->BcAdminForm->error('status') ?>
      </td>
    </tr>

    <tr v-show="!isGroupLink">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('required', __d('baser', '入力必須')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('required', [
          'type' => 'checkbox',
          'label' => __d('baser', '入力必須'),
          'v-model' => 'link.required'
        ]) ?>
        <?php echo $this->BcAdminForm->error('required') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('description', __d('baser', '説明文')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('description', [
          'type' => 'textarea',
          'v-model' => 'link.description'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '入力欄の右側に？マークのアイコンを付けツールチップで説明文を表示します。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('description') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('attention', __d('baser', '注意書き')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('attention', [
          'type' => 'text',
          'size' => 80,
          'v-model' => 'link.attention'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '入力欄の次の行に注意書きを表示します。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('attention') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('before_head', __d('baser', '前見出し')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('before_head', [
          'type' => 'text',
          'size' => 80,
          'v-model' => 'link.before_head'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '入力欄の前に文字列を表示します。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('before_head') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('after_head', __d('baser', '後見出し')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('after_head', [
          'type' => 'text',
          'size' => 80,
          'v-model' => 'link.after_head'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '入力欄の後に文字列を表示します。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('after_head') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('class', __d('baser', 'クラス')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('class', [
          'type' => 'text',
          'size' => 80,
          'v-model' => 'link.class'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', '必要であればHTMl用タグのクラスを指定します。<br>
          元々のコントロールに付与されているクラスも一緒に設定しないとコントロールのデザインが崩れてしあう場合があります。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('class') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('options', __d('baser', 'オプション')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('options', [
          'type' => 'text',
          'size' => 80,
          'v-model' => 'link.options'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'フィールドのコントロールに対して追加の属性を指定する場合に入力します。
            属性名と値をパイプ（|）で区切って指定します。複数属性を連続で指定する事ができます。<br>
            例）data-sample1|value1|data-sample2|value2
          ') ?>
        </div>
        <?php echo $this->BcAdminForm->error('options') ?>
      </td>
    </tr>

    <tr v-show="isGroupLink">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('group_valid', __d('baser', 'グループ機能')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('group_valid', [
          'type' => 'checkbox',
          'label' => __d('baser', 'グループチェック有効'),
          'v-model' => 'link.group_valid',
          '@change' => 'changeGroupFunction',
          ':disabled' => '!enabledGroupValid'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'グループ内における入力エラーが発生した場合に、グループ内の全てのフィールドの下にエラーメッセージをまとめたい場合にチェックを入れます。グループチェックを利用する場合はループ機能は利用できません。') ?>
        </div>&nbsp;&nbsp;
        <?php echo $this->BcAdminForm->control('use_loop', [
          'type' => 'checkbox',
          'label' => __d('baser', 'ループ機能を利用する'),
          'v-model' => 'link.use_loop',
          '@change' => 'changeGroupFunction',
          ':disabled' => '!enabledUseLoop'
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'ループ機能を利用するとグループ配下のフィールドを１セットとして複数セットの入力欄を作成する事ができます。ループ機能を利用するとグループチェックは利用できません。') ?>
        </div>
        <div v-show="(link.rght - link.lft) > 1"><small><?php echo __d('baser', '既に子となる関連フィールドが存在する場合にはループ機能は変更できません。') ?></small></div>
      </td>
    </tr>

    <tr v-show="!isGroupLink">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('display_admin_list', __d('baser', '管理画面での動作')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('display_admin_list', [
          'type' => 'checkbox',
          'label' => __d('baser', 'エントリー一覧に項目を表示する'),
          'v-model' => 'link.display_admin_list'
        ]) ?>
        <?php echo $this->BcAdminForm->control('before_linefeed', [
          'type' => 'checkbox',
          'label' => __d('baser', '入力欄の前に改行を入れる'),
          'v-model' => 'link.before_linefeed'
        ]) ?>
        <?php echo $this->BcAdminForm->control('after_linefeed', [
          'type' => 'checkbox',
          'label' => __d('baser', '入力欄の後に改行を入れる'),
          'v-model' => 'link.after_linefeed'
        ]) ?>
      </td>
    </tr>

    <tr v-show="!isGroupLink">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('display_front', __d('baser', 'テーマでの動作')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('display_front', [
          'type' => 'checkbox',
          'label' => __d('baser', 'テーマのヘルパーで呼び出せる'),
          'v-model' => 'link.display_front'
        ]) ?>
      </td>
    </tr>

    <tr v-show="!isGroupLink">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('use_api', __d('baser', 'Web API での動作')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('use_api', [
          'type' => 'checkbox',
          'label' => __d('baser', 'Web API の返却値に含める'),
          'v-model' => 'link.use_api'
        ]) ?>
      </td>
    </tr>

    <tr v-show="!isGroupLink">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('use_api', __d('baser', '検索での動作')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('search_target_admin', [
          'type' => 'checkbox',
          'label' => __d('baser', '管理画面において検索対象にする'),
          'v-model' => 'link.search_target_admin'
        ]) ?>
        <?php echo $this->BcAdminForm->control('search_target_front', [
          'type' => 'checkbox',
          'label' => __d('baser', 'テーマ、Web API において検索対象にする'),
          'v-model' => 'link.search_target_front'
        ]) ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>

  <?php echo $this->BcFormTable->dispatchAfter() ?>

  <?php echo $this->BcAdminForm->end() ?>

</section>
