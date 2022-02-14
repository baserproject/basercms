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

use BaserCore\Model\Entity\Site;
use BaserCore\View\BcAdminAppView;

/**
 * サブサイト一覧（行）
 * @var BcAdminAppView $this
 * @var int $count
 * @var Site $site
 * @var array $devices
 * @var array $langs
 * @var array $siteList
 */

$classies = [];
if ($site->status) {
  $classies = ['publish'];
} else {
  $classies = ['unpublish', 'disablerow'];
}
$class = ' class="' . implode(' ', $classies) . '"';
$site_alias = $site->alias ? '/' . $site->alias . '/' : '/';
$url = $this->BcAdminContent->getUrl($site_alias, true, $site->use_subdomain);
?>


<tr id="Row<?php echo $count ?>" <?php echo $class; ?>>
  <td class="bca-table-listup__tbody-td" style="width:5%"><?php echo $site->id; ?></td>
  <td class="bca-table-listup__tbody-td"><?php echo h($site->display_name) ?></td>
  <td
    class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($site->name, ['action' => 'edit', $site->id]); ?>
    <br>
    <?php echo $site->alias ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="width:5%;" class="bc-align-center status">
    <?php echo $this->BcText->booleanMark($site->status); ?><br>
  </td>
  <td class="bca-table-listup__tbody-td" class="bc-align-center">
    <?php echo $this->BcText->arrayValue($site->device, $devices, ''); ?><br>
    <?php echo $this->BcText->arrayValue($site->lang, $langs, ''); ?>
  </td>
  <td
    class="bca-table-listup__tbody-td"><?php echo h($this->BcText->arrayValue($site->main_site_id, $siteList, '')); ?>
    <br>
    <?php echo $site->theme ?>
  </td>
  <?php echo $this->BcListTable->dispatchShowRow($site) ?>
  <td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
    <?php echo $this->BcTime->format($site->created, 'yyyy-MM-dd') ?><br>
    <?php echo $this->BcTime->format($site->modified, 'yyyy-MM-dd') ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions" style="width:15%">
    <?php if ($site->status) : ?>
      <?php echo $this->BcAdminForm->postLink(
        '',
        ['action' => 'unpublish', $site->id],
        ['title' => __d('baser', '非公開'),
          'class' => 'btn-unpublish bca-btn-icon',
          'data-bca-btn-type' => 'unpublish',
          'data-bca-btn-size' => 'lg']
      ) ?>
      <?php $this->BcBaser->link('', $url, ['title' => __d('baser', '確認'), 'target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
    <?php else: ?>
      <?php echo $this->BcAdminForm->postLink(
        '',
        ['action' => 'publish', $site->id],
        ['title' => __d('baser', '公開'),
          'class' => 'btn-publish bca-btn-icon',
          'data-bca-btn-type' => 'publish',
          'data-bca-btn-size' => 'lg']
      ) ?>
    <?php endif ?>
    <?php $this->BcBaser->link('', ['action' => 'edit', $site->id], ['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
  </td>
</tr>
