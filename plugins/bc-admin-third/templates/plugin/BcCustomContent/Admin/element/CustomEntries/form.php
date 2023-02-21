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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var \BcCustomContent\Model\Entity\CustomEntry $entity
 * @var array $parents
 * @checked
 * @noTodo
 * @unitTest
 */
$creators = $this->BcAdminForm->getControlSource(
  'BcCustomContent.CustomEntries.creator_id',
  ($this->getRequest()->getParam('action') === 'add')? ['status' => true] : []
);
if (!$customTable->isContentTable()) {
  echo $this->BcAdminForm->control('status', ['type' => 'hidden', 'value' => true]);
}
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="bca-section" data-bca-section-type="form-group">
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($this->getRequest()->getParam('action') === 'edit'): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('id', 'No') ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo h($entity->id) ?>
        </td>
      </tr>
    <?php endif ?>

    <?php if ($customTable->has_child): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('parent_id', '親エントリー') ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('parent_id', [
            'type' => 'select',
            'options' => $this->BcAdminForm->getControlSource('BcCustomContent.CustomEntries.parent_id', ['selfId' => $entity->id]),
            'empty' => __d('baser', '指定しない')
          ]) ?>&nbsp;
          <?php echo $this->BcAdminForm->error('parent_id') ?>
        </td>
      </tr>
    <?php endif ?>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('title', 'タイトル') ?>&nbsp;
        <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', [
          'type' => 'text',
          'size' => 60,
          'placeholder' => 'エントリーのタイトルを入力します'
        ]) ?>&nbsp;
        <?php if ($customTable->isContentTable()): ?>
          <small>[スラッグ]</small>
          <?php echo $this->BcAdminForm->control('name', [
            'type' => 'text',
            'size' => 30,
            'placeholder' => 'スラッグ'
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <div class="bca-helptext">
            <?php echo __d('baser', 'スラッグはURLで利用します。スラッグを入力すると、次のようなURLでアクセスできますが入力しない場合はエントリーNOを利用します。<br>/content-name/view/slag') ?>
          </div>
        <?php endif ?>
        <?php echo $this->BcAdminForm->error('title') ?>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>

    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('published', '登録情報') ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if ($customTable->isContentTable()): ?>
          <?php echo $this->BcAdminForm->control('published', [
            'type' => 'dateTimePicker',
            'size' => 12,
            'maxlength' => 10,
            'dateLabel' => ['text' => '公開日付'],
            'timeLabel' => ['text' => '公開時間']
          ]) ?>&nbsp;&nbsp;
        <?php endif ?>
        <?php echo $this->BcAdminForm->label('creator_id', '作成者') ?>
        <?php if (\BaserCore\Utility\BcUtil::isAdminUser()): ?>
          <?php echo $this->BcAdminForm->control('creator_id', [
            'type' => 'select',
            'options' => $creators
          ]) ?>
        <?php else: ?>
          &nbsp;&nbsp;<?php echo $creators[$entity->creator_id] ?>
          <?php echo $this->BcAdminForm->hidden('creator_id', ['type' => 'hidden']) ?>
        <?php endif ?>
        <?php echo $this->BcAdminForm->error('published') ?>
        <?php echo $this->BcAdminForm->error('creator_id') ?>
      </td>
    </tr>


    <?php if ($customTable->isContentTable()): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('status', '公開状態') ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('status', [
            'type' => 'radio',
            'options' => [0 => __d('baser', '非公開'), 1 => __d('baser', '公開')]
          ]) ?>
          <?php echo $this->BcAdminForm->error('status') ?>
        </td>
      </tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('publish_begin', '公開期間') ?>
        </th>
        <td class="col-input bca-form-table__input">
        <span class="bca-datetimepicker__group">
          <span class="bca-datetimepicker__start">
            <?php echo $this->BcAdminForm->control('publish_begin', [
              'type' => 'dateTimePicker',
              'size' => 12,
              'maxlength' => 10,
              'dateLabel' => ['text' => '開始日付'],
              'timeLabel' => ['text' => '開始時間']
            ]) ?>
          </span>
          <span class="bca-datetimepicker__delimiter">〜</span>
          <span class="bca-datetimepicker__end">
            <?php echo $this->BcAdminForm->control('publish_end', [
              'type' => 'dateTimePicker',
              'size' => 12,
              'maxlength' => 10,
              'dateLabel' => ['text' => '終了日付'],
              'timeLabel' => ['text' => '終了時間']
            ]) ?>
            </span>
        </span>
          <?php echo $this->BcAdminForm->error('publish_begin') ?>
          <?php echo $this->BcAdminForm->error('publish_end') ?>
        </td>
      </tr>
    <?php endif ?>
  </table>
</div>

<div class="bca-section" data-bca-section-type="form-group">
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($customTable->custom_links): ?>
      <?php foreach($customTable->custom_links as $customLink): ?>
        <?php /** @var \BcCustomContent\Model\Entity\CustomLink $customLink */ ?>
        <?php if (!$this->CustomContent->isEnableField($customLink)) continue ?>
        <tr>
          <th class="col-head bca-form-table__label">
            <?php echo $this->CustomContentAdmin->label($customLink) ?>&nbsp;&nbsp;
            <?php echo $this->CustomContentAdmin->required($customLink) ?>
          </th>
          <td class="col-input bca-form-table__input">
            <?php
            if ($customLink->custom_field->type === 'group') {
              if ($customLink->use_loop) {
                $this->BcBaser->element('CustomEntries/form_loop', ['customLink' => $customLink]);
              } else {
                $this->BcBaser->element('CustomEntries/form_group', ['customLink' => $customLink]);
              }
            } else {
              $this->BcBaser->element('CustomEntries/form_field', [
                'customLink' => $customLink,
                'parent' => null
              ]);
            }
            ?>
          </td>
        </tr>
      <?php endforeach ?>
    <?php endif ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>
