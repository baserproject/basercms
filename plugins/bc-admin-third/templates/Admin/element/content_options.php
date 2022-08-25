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

use BaserCore\Model\Entity\Content;

/**
 * コンテンツオプション
 * @var \BaserCore\View\BcAdminAppView $this
 * @var bool $editableContent コンテンツ編集不可かどうか
 * @var array $layoutTemplates レイアウトテンプレートリスト
 * @var array $authorList
 * @var Content $content
 * @var string $entityName
 */

$editable = $this->BcContents->isEditable($content);
$created_date = $this->BcAdminForm->getSourceValue($entityName . "created_date");
$modified_date = $this->BcAdminForm->getSourceValue($entityName . "modified_date");
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
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($entityName . "description", __d('baser', '説明文')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if ($editable): ?>
            <?php echo $this->BcAdminForm->control($entityName . "description", ['type' => 'textarea', 'cols' => 36, 'rows' => 4, 'data-input-text-size' => 'full-counter']) ?>
          <?php else: ?>
            <?php if ($this->BcAdminForm->getSourceValue($entityName . "exclude_search")): ?>
              <?php echo h($this->BcAdminForm->getSourceValue($entityName . "description")) ?>
            <?php else: ?>
              <?php echo h($this->BcSiteConfig->getValue("description")) ?>
            <?php endif ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "description") ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error($entityName . "description") ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($entityName . "eyecatch", __d('baser', 'アイキャッチ')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if ($editable): ?>
            <?php echo $this->BcAdminForm->control($entityName . "eyecatch", ['type' => 'file', 'imgsize' => 'thumb', 'novalidate' => true]) ?>
          <?php else: ?>
            <?php echo $this->BcUpload->uploadImage($entityName . "eyecatch", $this->BcAdminForm->getSourceValue($entityName . "eyecatch"), ['imgsize' => 'thumb']); ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error($entityName . "eyecatch") ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($entityName . "author_id", __d('baser', '作成者')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if ($editable): ?>
            <?php echo $this->BcAdminForm->control($entityName . 'author_id', ['type' => 'select', 'options' => $authorList]) ?>
            <small>[<?php echo __d('baser', '作成日') ?>
              ]</small> <?php echo $this->BcAdminForm->control($entityName . 'created_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10, 'value' => $created_date]); ?>
            <small>[<?php echo __d('baser', '更新日') ?>
              ]</small> <?php echo $this->BcAdminForm->control($entityName . 'modified_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10, 'value' => $modified_date]); ?>
          <?php else: ?>
            <?php echo h($this->BcText->arrayValue($this->BcAdminForm->getSourceValue($entityName . "author_id"), $authorList)) ?>
            <small>[<?php echo __d('baser', '作成日') ?>]</small> <?= $created_date ?>
            <small>[<?php echo __d('baser', '更新日') ?>]</small> <?= $modified_date ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "author_id") ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "created_date") ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "modified_date") ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error($entityName . "author_id") ?>
          <?php echo $this->BcAdminForm->error($entityName . "created_date") ?>
          <?php echo $this->BcAdminForm->error($entityName . "modified_date") ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($entityName . "layout_template", __d('baser', 'レイアウトテンプレート')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control($entityName . "layout_template", ['type' => 'select', 'options' => $layoutTemplates]) ?>

          <?php echo $this->BcAdminForm->error($entityName . "layout_template") ?>
        </td>
      </tr>
      <tr>
        <th
          class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label($entityName . "exclude_search", __d('baser', 'その他設定')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php if ($editable): ?>
            <?php if (Cake\Core\Plugin::isloaded('BcSearchIndex')): ?>
              <span style="white-space: nowrap">
                <?php echo $this->BcAdminForm->control($entityName . "exclude_search", [
                  'type' => 'checkbox',
                  'label' => __d('baser', 'サイト内検索の検索結果より除外する')
                ]) ?>
              </span>
            <?php endif ?>
            <span style="white-space: nowrap">
              <?php echo $this->BcAdminForm->control($entityName . "exclude_menu", [
                'type' => 'checkbox',
                'label' => __d('baser', '公開ページのメニューより除外する')
              ]) ?>
            </span>
            <span style="white-space: nowrap">
              <?php echo $this->BcAdminForm->control($entityName . "blank_link", [
                'type' => 'checkbox',
                'label' => __d('baser', 'メニューのリンクを別ウィンドウ開く')
              ]) ?>
            </span>
          <?php else: ?>
            <?php if (Cake\Core\Plugin::isloaded('BcSearchIndex')): ?>
              <?php if ($this->BcAdminForm->getSourceValue($entityName . "exclude_search")): ?>
                <span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外する') ?></span>
              <?php else: ?>
                <span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外しない') ?></span>
              <?php endif ?>
            <?php endif ?>
            <?php if ($this->BcAdminForm->getSourceValue($entityName . "exclude_menu")): ?>
              <span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外する') ?></span>
            <?php else: ?>
              <span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外しない') ?></span>
            <?php endif ?>
            <?php if ($this->BcAdminForm->getSourceValue($entityName . "blank_link")): ?>
              <span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを別ウィンドウ開く') ?></span>
            <?php else: ?>
              <span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを同じウィンドウに開く') ?></span>
            <?php endif ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "exclude_search") ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "exclude_menu") ?>
            <?php echo $this->BcAdminForm->hidden($entityName . "blank_link") ?>
          <?php endif ?>
        </td>
      </tr>
    </table>
  </div>
</section>
