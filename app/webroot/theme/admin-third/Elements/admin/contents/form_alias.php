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
 * @var BcAppView $this
 * @var bool $related
 */

if ((!empty($this->BcContents->settings[$srcContent['type']]))) {
	$title = $this->BcContents->settings[$srcContent['type']]['title'];
	$editLink = $this->BcContents->settings[$srcContent['type']]['routes']['edit'];
	$editLink = array_merge($editLink, [
		$srcContent['entity_id'],
		'content_id' => $srcContent['id'],
		'parent_id' => $srcContent['parent_id']
	]);
} else {
	$title = __d('baser', '無所属コンテンツ');
	$editLink = '/' . BcUtil::getAdminPrefix() . '/contents/edit';
	if ($srcContent['entity_id']) {
		$editLink .= '/' . $srcContent['entity_id'];
		$editLink .= '/content_id:' . $srcContent['id'] . '/parent_id:' . $srcContent['parent_id'];
	}
}

?>


<table class="form-table bca-form-table">
	<tr>
		<th class=" bca-form-table__label"><?php echo $this->BcForm->label('Content.alias_id', __d('baser', '元コンテンツ')) ?></th>
		<td class="bca-form-table__input">
			<?php echo $this->BcForm->input('Content.alias_id', ['type' => 'hidden']) ?>
			<small>[<?php echo $title ?>]</small>&nbsp;
			&nbsp;
			<?php $this->BcBaser->link($srcContent['title'], $editLink, ['target' => '_blank', 'escape' => true]) ?>
			<?php if ($related): ?>
				<p><?php echo __d('baser', 'このコンテンツはメインサイトの連携エイリアスです。<br>フォルダ、レイアウトテンプレート以外を編集する場合は上記リンクをクリックしてメインサイトのコンテンツを編集してください。') ?></p>
			<?php endif ?>
		</td>
	</tr>
</table>
