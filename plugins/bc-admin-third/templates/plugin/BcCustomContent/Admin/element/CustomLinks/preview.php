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
 * 関連フィールド　プレビュー
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $entity
 * @var \Cake\ORM\ResultSet $flatLinks
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<div id="CustomLinkPreview" hidden>

<?php echo $this->CustomContentAdmin->BcAdminForm->create(null) ?>

  <div class="custom-link-preview-inner">
    <span class="preview-icon">PREVIEW</span>
    <table class="form-table bca-form-table" data-bca-table-type="type2">
    <?php foreach($flatLinks as $link): ?>
      <?php if ($link->custom_field->type === 'group') continue ?>
      <tr v-show="showPreview['<?php echo $link->id ?>']">
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label($link->name, '{{link.title}}') ?>&nbsp;&nbsp;
          <span class="bca-label" data-bca-label-type="required" v-show="link.required"><?php echo __d('baser', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <span>{{ link.before_head }}</span>
          <?php echo $this->CustomContentAdmin->control($link) ?>
          <span>{{ link.after_head }}</span>
          <i class="bca-icon--question-circle bca-help" v-show="link.description"></i>
          <div class="bca-helptext" v-html="linkHtmlDescription">
          </div>
          <div class="bca-attention"><small>{{ link.attention }}</small></div>
        </td>
      </tr>
    <?php endforeach ?>
    </table>
    <div v-show="showPreview['NonSupport']" style="text-align: center">プレビュー未対応です</div>
  </div>

<?php echo $this->CustomContentAdmin->BcAdminForm->end() ?>

</div>
