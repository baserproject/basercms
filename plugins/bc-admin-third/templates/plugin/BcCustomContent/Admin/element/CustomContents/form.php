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
 * カスタムコンテンツ / フォーム
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var array $customTables
 * @var \BcCustomContent\Model\Entity\CustomContent $entity
 * @var bool $editorEnterBr
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<section class="bca-section" data-bca-section-type="form-group">
  <table class="form-table bca-form-table" data-bca-table-type="type2">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('description', __d('baser_core', '説明文')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php
        echo $this->BcAdminForm->editor('description', [
          'editorWidth' => 'auto',
          'editorHeight' => '120px',
          'editorToolType' => 'simple',
          'editorEnterBr' => $editorEnterBr
        ])
        ?>
        <?php echo $this->BcAdminForm->error('description') ?>
      </td>
    </tr>

    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('custom_table_id', __d('baser_core', 'テーブル')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('custom_table_id', [
          'type' => 'select',
          'options' => $customTables,
          'empty' => __d('baser_core', 'テーブルを選択してください')
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser_core', 'コンテンツに紐付けるテーブルを選択します。') ?>
        </div>
        &nbsp;&nbsp;<?php $this->BcBaser->link(__d('baser_core', 'テーブル設定に移動'), [
          'controller' => 'CustomTables',
          'action' => 'edit',
          $entity->custom_table_id
        ], ['class' => 'button-small']) ?>
        <?php echo $this->BcAdminForm->error('custom_table_id') ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

  </table>
</section>

<?php if($entity->custom_table_id): ?>
<section class="bca-section" data-bca-section-type="form-group">

  <div class="bca-collapse__action">
    <button
      type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#blogContentsSettingBody"
      aria-expanded="false"
      aria-controls="blogContentsSettingBody">
      詳細設定&nbsp;&nbsp;
      <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>

  <div class="bca-collapse" id="blogContentsSettingBody" data-bca-state="">
    <table class="form-table bca-form-table" data-bca-table-type="type2">

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('list_count', __d('baser_core', '一覧表示件数')) ?>&nbsp;
          <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('list_count', ['type' => 'text', 'size' => 10, 'maxlength' => 255]) ?>
          &nbsp;件&nbsp;
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('list_count') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'フロントエンドの一覧に表示する件数を指定します。') ?></li>
              <li><?php echo __d('baser_core', '半角数字で入力してください。') ?></li>
            </ul>
          </div>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('list_order', __d('baser_core', '一覧の並び順')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('list_order', [
            'type' => 'select',
            'options' => $this->BcAdminForm->getControlsource('BcCustomContent.CustomContents.list_order', [
              'custom_table_id' => $entity->custom_table_id
            ])
          ]) ?>&nbsp;&nbsp;
          <?php echo $this->BcAdminForm->control('list_direction', [
            'type' => 'select',
            'options' => [
              'DESC' => __d('baser_core', '降順'),
              'ASC' => __d('baser_core', '昇順')
            ]]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('list_order') ?>
          <?php echo $this->BcAdminForm->error('list_direction') ?>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'フロントエンドの一覧におけるエントリーの並び順を指定します。') ?>
          </div>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('template', __d('baser_core', 'コンテンツテンプレート名')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('template', [
            'type' => 'select',
            'options' => $this->BcAdminForm->getControlsource('BcCustomContent.CustomContents.template', [
              'site_id' => $this->getRequest()->getAttribute('currentSite')->id
            ]),
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('template') ?>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'フロントエンドで利用するテンプレートを指定します。') ?>
          </div>
        </td>
      </tr>

      <?php if($this->BcBaser->isPluginLoaded('BcWidgetArea')): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('widget_area', __d('baser_core', 'ウィジェットエリア')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('widget_area', [
            'type' => 'select',
            'options' => $this->BcAdminForm->getControlsource('BcWidgetArea.WidgetAreas.id'),
            'empty' => __d('baser_core', 'サイト基本設定に従う')
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('widget_area') ?>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'フロントエンドで利用するウィジェットエリアを指定します。') ?><br>
            <?php echo __d('baser_core', 'ウィジェットエリアはウィジェットエリア管理より追加できます。') ?><br>
            <ul>
              <li><?php $this->BcBaser->link(__d('baser_core', 'ウィジェットエリア管理'), [
                'plugin' => 'BcWidgetArea',
                'controller' => 'WidgetAreas',
                'action' => 'index'
              ]) ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <?php endif ?>

      <?php echo $this->BcAdminForm->dispatchAfterForm('option') ?>

    </table>
  </div>
</section>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>
