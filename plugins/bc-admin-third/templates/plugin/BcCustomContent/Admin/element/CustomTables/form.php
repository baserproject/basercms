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
 * カスタムテーブル / フォーム
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $entity
 * @var \Cake\ORM\ResultSet $fields
 * @var array $customLinks
 * @var \Cake\ORM\ResultSet $flatLinks
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcCustomContent.admin/custom_tables/form.bundle', false, [
  'id' => 'AdminCustomTablesFormScript',
  'data-setting' => json_encode(\Cake\Core\Configure::read('BcCustomContent.fieldTypes')),
  'data-links' => json_encode($flatLinks),
  'defer' => true
]);
$this->BcAdminForm->unlockField("custom_links");
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="bca-section" data-bca-section-type="form-group">
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($this->getRequest()->getParam('action') === 'edit'): ?>
      <tr>
        <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo h($entity->id) ?>
          <?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
        </td>
      </tr>
    <?php endif ?>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('type', __d('baser', 'テーブルタイプ')) ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('type', [
          'type' => 'radio',
          'options' => [1 => __d('baser', 'コンテンツ'), 2 => __d('baser', 'マスタ')]
        ]) ?>&nbsp;&nbsp;
        <span id="SpanHasChild" hidden>（&nbsp;<?php echo $this->BcAdminForm->control('has_child', [
            'type' => 'checkbox',
            'label' => __d('baser', '階層構造を持つ')
          ]) ?>）
        &nbsp;<i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'テーブルタイプでコンテンツを選択すると、コンテンツ管理にて配置する事ができます。
          マスタを選択すると他のテーブルのラジオボタンやセレクトボックスのデータソースとして利用する事ができます。<br><br>
          なお、マスタの場合は、データの階層構造を持てるように設定する事ができます。') ?>
        </div>
        </span>
        <?php echo $this->BcAdminForm->error('type') ?>
        <?php echo $this->BcAdminForm->error('has_child') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', '識別名')) ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('title', __d('baser', 'タイトル')) ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
        <?php echo $this->BcAdminForm->error('title') ?>
      </td>
    </tr>

    <tr id="RowDisplayField">
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('display_field', __d('baser', '表示名称フィールド')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('display_field', [
          'type' => 'select',
          'options' => $this->BcAdminForm->getControlSource('BcCustomContent.CustomTables.display_field', ['id' => $entity->id])
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'エントリーの登録、編集、削除時に表示名称として利用するフィールドを指定します。指定しない場合は、初期状態で保持する No フィールドを利用します。') ?>
        </div>
        <?php echo $this->BcAdminForm->error('display_field') ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<?php if ($this->getRequest()->getParam('action') !== 'edit') return ?>

<div id="CustomFieldSetting" class="custom-field-setting">

  <div class="custom-field-setting__panel">
    <h2>利用中のフィールド</h2>
    <div id="CustomFieldSettingTarget">
      <?php if ($customLinks): ?>
        <?php $i = 1 ?>
        <?php foreach($customLinks as $customLink): ?>
          <?php if (!$customLink->children): ?>
            <?php $this->BcBaser->element('CustomTables/in_use_field', ['customLink' => $customLink, 'i' => $i++]) ?>
          <?php else: ?>
            <?php $this->BcBaser->element('CustomTables/in_use_group_field', ['customLink' => $customLink, 'i' => $i++]) ?>
          <?php endif ?>
        <?php endforeach ?>
      <?php endif ?>
    </div>
  </div>

  <div class="custom-field-setting__panel">
    <h2>
      利用できるフィールド&nbsp;&nbsp;
      <?php $this->BcBaser->link(__d('baser', '新規登録'),
        ['controller' => 'CustomFields', 'action' => 'add'], [
          'class' => 'bca-btn',
          'data-bca-btn-type' => 'add',
          'data-bca-btn-size' => 'sm'
        ]) ?>
    </h2>
    <div id="CustomFieldSettingSource">
      <?php if ($fields->count()): ?>
        <?php foreach($fields as $field): ?>
          <?php $this->BcBaser->element('CustomTables/available_field', ['field' => $field]) ?>
        <?php endforeach ?>
      <?php endif ?>
    </div>
  </div>

</div>

