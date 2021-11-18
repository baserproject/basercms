<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * コンテンツオプション
 * @var bool $disableEditContent コンテンツ編集不可かどうか
 * @var array $layoutTemplates レイアウトテンプレートリスト
 */
$disableEdit = $this->BcContents->isEditable();
$authors = $this->BcAdminContent->getAuthors();
$created_date = $this->BcAdminForm->getSourceValue($contentPath . 'created_date');
$modified_date = $this->BcAdminForm->getSourceValue($contentPath . 'modified_date');
?>


<section id="ContentsOptionSetting" class="bca-section" data-bca-section-type="form-group">
  <div class="bca-collapse__action">
    <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
            data-bca-target="#formContentsOptionBody" aria-expanded="false" aria-controls="formOptionBody">オプション&nbsp;&nbsp;<i
        class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
  </div>
  <div class="bca-collapse" id="formContentsOptionBody" data-bca-state="">
    <table class="form-table bca-form-table" data-bca-table-type="type2">
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'description', __d('baser', '説明文')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if (!$disableEdit): ?>
            <?php echo $this->BcAdminForm->control($contentPath . 'description', ['type' => 'textarea', 'cols' => 36, 'rows' => 4, 'data-input-text-size' => 'full-counter']) ?>
          <?php else: ?>
            <?php if ($this->BcAdminForm->getSourceValue($contentPath . 'exclude_search')): ?>
              <?php echo h($this->BcAdminForm->getSourceValue($contentPath . 'description')) ?>
            <?php else: ?>
              <?php echo h($this->BcBaser->siteConfig['description']) ?>
            <?php endif ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'description') ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error($contentPath . 'description') ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'eyecatch', __d('baser', 'アイキャッチ')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if (!$disableEdit): ?>
            <?php echo $this->BcAdminForm->control($contentPath . 'eyecatch', ['type' => 'file', 'imgsize' => 'thumb',  'novalidate' => true]) ?>
          <?php else: ?>
            <?php echo $this->BcUpload->uploadImage($contentPath . 'eyecatch', $this->BcAdminForm->getSourceValue($contentPath . 'eyecatch'), ['imgsize' => 'thumb']); ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error($contentPath . 'eyecatch') ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'author_id', __d('baser', '作成者')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if (!$disableEdit): ?>
            <?php echo $this->BcAdminForm->control($contentPath . 'author_id', ['type' => 'select', 'options' => $authors]) ?>
            <small>[<?php echo __d('baser', '作成日') ?>
              ]</small> <?php echo $this->BcAdminForm->control($contentPath . 'created_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10, 'value' => $created_date]); ?>
            <small>[<?php echo __d('baser', '更新日') ?>
              ]</small> <?php echo $this->BcAdminForm->control($contentPath . 'modified_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10, 'value' => $modified_date]); ?>
          <?php else: ?>
            <?php echo h($this->BcText->arrayValue($this->BcAdminForm->getSourceValue($contentPath . 'author_id'), $authors)) ?>
            <small>[<?php echo __d('baser', '作成日') ?>]</small> <?= $created_date ?>
            <small>[<?php echo __d('baser', '更新日') ?>]</small> <?= $modified_date ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'author_id') ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'created_date') ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'modified_date') ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error($contentPath . 'author_id') ?>
          <?php echo $this->BcAdminForm->error($contentPath . 'created_date') ?>
          <?php echo $this->BcAdminForm->error($contentPath . 'modified_date') ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'layout_template', __d('baser', 'レイアウトテンプレート')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control($contentPath . 'layout_template', ['type' => 'select', 'options' => $layoutTemplates]) ?>

          <?php echo $this->BcAdminForm->error($contentPath . 'layout_template') ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($contentPath . 'exclude_search', __d('baser', 'その他設定')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if (!$disableEdit): ?>
            <span
              style="white-space: nowrap"><?php echo $this->BcAdminForm->control($contentPath . 'exclude_search', ['type' => 'checkbox', 'label' => __d('baser', 'サイト内検索の検索結果より除外する')]) ?></span>
            <span
              style="white-space: nowrap"><?php echo $this->BcAdminForm->control($contentPath . 'exclude_menu', ['type' => 'checkbox', 'label' => __d('baser', '公開ページのメニューより除外する')]) ?></span>
            <span
              style="white-space: nowrap"><?php echo $this->BcAdminForm->control($contentPath . 'blank_link', ['type' => 'checkbox', 'label' => __d('baser', 'メニューのリンクを別ウィンドウ開く')]) ?></span>
          <?php else: ?>
            <?php if ($this->BcAdminForm->getSourceValue($contentPath . 'exclude_search')): ?>
              <span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外する') ?></span>
            <?php else: ?>
              <span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外しない') ?></span>
            <?php endif ?>
            <?php if ($this->BcAdminForm->getSourceValue($contentPath . 'exclude_menu')): ?>
              <span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外する') ?></span>
            <?php else: ?>
              <span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外しない') ?></span>
            <?php endif ?>
            <?php if ($this->BcAdminForm->getSourceValue($contentPath . 'blank_link')): ?>
              <span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを別ウィンドウ開く') ?></span>
            <?php else: ?>
              <span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを同じウィンドウに開く') ?></span>
            <?php endif ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'exclude_search') ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'exclude_menu') ?>
            <?php echo $this->BcAdminForm->hidden($contentPath . 'blank_link') ?>
          <?php endif ?>
        </td>
      </tr>
    </table>
  </div>
</section>
