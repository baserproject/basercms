<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */
?>

<style>
#SeoSettingBody img {
    max-width: 400px;
    max-height: 200px;
}
</style>

<?php echo $this->BcAdminForm->hidden('seo_meta.id') ?>
<?php echo $this->BcAdminForm->hidden('seo_meta.table_alias', ['value' => $tableAlias]) ?>
<?php echo $this->BcAdminForm->hidden('seo_meta.table_id', ['value' => $tableId]) ?>
<?php echo $this->BcAdminForm->hidden('seo_meta.entity_id') ?>

<section class="bca-section" data-bca-section-type='form-group'>
  <div class="bca-collapse__action">
    <button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
      data-bca-target="#SeoSettingBody" aria-expanded="false"
      aria-controls="SeoSettingBody"
    >
      SEO設定
      <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="SeoSettingBody" data-bca-state="">
    <table class="form-table bca-form-table section">
      <?php foreach ($seoFields as $fieldName => $fieldProp): ?>
        <tr>
          <th class="col-head bca-form-table__label">
            <?php echo $this->BcAdminForm->label('seo_meta.' . $fieldName, $fieldProp['title']) ?>
          </th>
          <td class="col-input bca-form-table__input">
            <?php if ($fieldProp['type'] == 'text'): ?>
              <?php echo $this->BcAdminForm->control('seo_meta.' . $fieldName , [
                'type' => 'text',
                'size' => '50',
              ]) ?>
            <?php elseif ($fieldProp['type'] == 'textarea'): ?>
              <?php echo $this->BcAdminForm->control('seo_meta.' . $fieldName, [
                'type' => 'textarea',
                'cols' => '36',
                'rows' => '5',
              ]) ?>
            <?php elseif ($fieldProp['type'] == 'file'): ?>
              <?php echo $this->BcUpload->setTable('BcSeo.SeoMetas'); ?>
              <?php echo $this->BcAdminForm->control('seo_meta.' . $fieldName, [
                'type' => 'file',
              ]) ?>
            <?php endif; ?>
            <?php echo $this->BcAdminForm->error('seo_meta.' . $fieldName) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</section>
