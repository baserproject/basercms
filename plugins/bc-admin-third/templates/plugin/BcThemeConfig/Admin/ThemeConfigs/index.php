<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ設定編集
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcThemeConfig\Model\Entity\ThemeConfig $themeConfig
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->css('vendor/colpick', ['inline' => false]);
$this->BcBaser->js(['vendor/colpick'], false);
$this->BcAdmin->setTitle(__d('baser', 'テーマ設定'));
$this->BcAdmin->setHelp('theme_configs_form');
?>


<script type="text/javascript">
  $(function () {
    $(".color-picker").each(function () {
      var color;
      if ($(this).val()) {
        $(this).css('border-right', '36px solid #' + $(this).val());
        color = $(this).val();
      } else {
        color = 'ffffff';
      }
      $(this).colpick({
        layout: 'hex',
        color: color,
        onSubmit: function (hsb, hex, rgb, el) {
          $(el).val(hex).css('border-right', '36px solid #' + hex);
          $(el).colpickHide();
        }
      });
    });
  });
</script>


<?php echo $this->BcAdminForm->create($themeConfig, ['type' => 'file']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<table class="form-table">
  <tr>
    <th><?php echo __d('baser', 'テーマカラー') ?></th>
    <td>
      <small>[<?php echo __d('baser', 'メイン') ?>]</small>
      #<?php echo $this->BcAdminForm->control('color_main', ['type' => 'text', 'size' => 6, 'class' => 'bca-textbox__input color-picker']) ?>
      　
      <small>[<?php echo __d('baser', 'サブ') ?>]</small>
      #<?php echo $this->BcAdminForm->control('color_sub', ['type' => 'text', 'size' => 6, 'class' => 'bca-textbox__input color-picker']) ?>
      <br>
      <small>[<?php echo __d('baser', 'テキストリンク') ?>]</small>
      #<?php echo $this->BcAdminForm->control('color_link', ['type' => 'text', 'size' => 6, 'class' => 'bca-textbox__input color-picker']) ?>
      　
      <small>[<?php echo __d('baser', 'テキストホバー') ?>]</small>
      #<?php echo $this->BcAdminForm->control('color_hover', ['type' => 'text', 'size' => 6, 'class' => 'bca-textbox__input color-picker']) ?>
    </td>
  </tr>
  <tr>
    <th><?php echo __d('baser', 'ロゴ') ?></th>
    <td>
      <p><?php $this->BcThemeConfig->logo(['thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
      <p><?php echo $this->BcAdminForm->control('logo', ['type' => 'file']) ?><?php if ($themeConfig->logo): ?><?php echo $this->BcAdminForm->control('logo_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する')]) ?><?php endif ?><?php echo $this->BcAdminForm->error('logo') ?></p>
      <?php echo $this->BcAdminForm->control('logo_alt', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', '説明文') ?>]</small><br>
      <?php echo $this->BcAdminForm->control('logo_link', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
    </td>
  </tr>
  <tr>
    <th><?php echo __d('baser', 'メインイメージ１') ?></th>
    <td>
      <p><?php $this->BcThemeConfig->mainImage(['num' => 1, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
      <p><?php echo $this->BcAdminForm->control('main_image_1', ['type' => 'file']) ?><?php if ($themeConfig->main_image_1): ?><?php echo $this->BcAdminForm->control('main_image_1_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する')]) ?><?php endif ?><?php echo $this->BcAdminForm->error('main_image_1') ?></p>
      <?php echo $this->BcAdminForm->control('main_image_alt_1', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', '説明文') ?>]</small><br>
      <?php echo $this->BcAdminForm->control('main_image_link_1', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
    </td>
  </tr>
  <tr>
    <th><?php echo __d('baser', 'メインイメージ２') ?></th>
    <td>
      <p><?php $this->BcThemeConfig->mainImage(['num' => 2, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
      <p><?php echo $this->BcAdminForm->control('main_image_2', ['type' => 'file']) ?><?php if ($themeConfig->main_image_2): ?><?php echo $this->BcAdminForm->control('main_image_2_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する')]) ?><?php endif ?><?php echo $this->BcAdminForm->error('main_image_2') ?></p>
      <?php echo $this->BcAdminForm->control('main_image_alt_2', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', '説明文') ?>]</small><br>
      <?php echo $this->BcAdminForm->control('main_image_link_2', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
    </td>
  </tr>
  <tr>
    <th><?php echo __d('baser', 'メインイメージ３') ?></th>
    <td>
      <p><?php $this->BcThemeConfig->mainImage(['num' => 3, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
      <p><?php echo $this->BcAdminForm->control('main_image_3', ['type' => 'file']) ?><?php if ($themeConfig->main_image_3): ?><?php echo $this->BcAdminForm->control('main_image_3_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する')]) ?><?php endif ?><?php echo $this->BcAdminForm->error('main_image_3') ?></p>
      <?php echo $this->BcAdminForm->control('main_image_alt_3', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', '説明文') ?>]</small><br>
      <?php echo $this->BcAdminForm->control('main_image_link_3', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
    </td>
  </tr>
  <tr>
    <th><?php echo __d('baser', 'メインイメージ４') ?></th>
    <td>
      <p><?php $this->BcThemeConfig->mainImage(['num' => 4, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
      <p><?php echo $this->BcAdminForm->control('main_image_4', ['type' => 'file']) ?><?php if ($themeConfig->main_image_4): ?><?php echo $this->BcAdminForm->control('main_image_4_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する')]) ?><?php endif ?><?php echo $this->BcAdminForm->error('main_image_4') ?></p>
      <?php echo $this->BcAdminForm->control('main_image_alt_4', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', '説明文') ?>]</small><br>
      <?php echo $this->BcAdminForm->control('main_image_link_4', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
    </td>
  </tr>
  <tr>
    <th><?php echo __d('baser', 'メインイメージ５') ?></th>
    <td>
      <p><?php $this->BcThemeConfig->mainImage(['num' => 5, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
      <p><?php echo $this->BcAdminForm->control('main_image_5', ['type' => 'file']) ?><?php if ($themeConfig->main_image_5): ?><?php echo $this->BcAdminForm->control('main_image_5_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する')]) ?><?php endif ?><?php echo $this->BcAdminForm->error('main_image_5') ?></p>
      <?php echo $this->BcAdminForm->control('main_image_alt_5', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', '説明文') ?>]</small><br>
      <?php echo $this->BcAdminForm->control('main_image_link_5', ['type' => 'text', 'size' => 50]) ?>
      <small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
    </td>
  </tr>
  <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
  <?php echo $this->BcAdminForm->button(__d('baser', '保存'), [
    'div' => false,
    'class' => 'button bca-btn',
    'data-bca-btn-type' => 'save',
    'data-bca-btn-size' => 'lg',
    'data-bca-btn-width' => 'lg',
    'id' => 'BtnSave'
  ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
