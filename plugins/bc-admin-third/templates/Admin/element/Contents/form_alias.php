<?php

use BaserCore\Utility\BcUtil;
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
 * @var bool $related
 * @var \BaserCore\Model\Entity\Content $content
 */

if ((!empty($this->BcContents->getConfig('items')[$content->type]))) {
  $title = $this->BcContents->getConfig('items')[$content->type]['title'];
  $editLink = $this->BcContents->getConfig('items')[$content->type]['routes']['edit'];
  $editLink = array_merge($editLink, [
    $content->entity_id,
    'content_id' => $content->id,
    'parent_id' => $content->parent_id
  ]);
} else {
  $title = __d('baser', '無所属コンテンツ');
  $editLink = '/' . BcUtil::getAdminPrefix() . '/contents/edit';
  if ($content->entity_id) {
    $editLink .= '/' . $content->entity_id;
    $editLink .= '/content_id:' . $content->id . '/parent_id:' . $content->parent_id;
  }
}

?>


<table class="form-table bca-form-table">
  <tr>
    <th class=" bca-form-table__label">
      <?php echo $this->BcAdminForm->label('content.alias_id', __d('baser', '元コンテンツ')) ?>
    </th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('content.alias_id', ['type' => 'hidden']) ?>
      <small>[<?php echo $title ?>]</small>&nbsp;
      &nbsp;
      <?php $this->BcBaser->link($content->title, $editLink, ['target' => '_blank']) ?>
      <?php if ($related): ?>
        <p><?php echo __d('baser', 'このコンテンツはメインサイトの連携エイリアスです。<br>フォルダ、レイアウトテンプレート以外を編集する場合は上記リンクをクリックしてメインサイトのコンテンツを編集してください。') ?></p>
      <?php endif ?>
    </td>
  </tr>
</table>
