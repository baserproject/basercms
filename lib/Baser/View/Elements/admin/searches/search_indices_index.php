<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 検索インデックス一覧　検索ボックス
 */
$priorities = ['0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
	'0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0'];
$types = BcUtil::unserialize($this->BcBaser->siteConfig['content_types']);
?>


<?php echo $this->BcForm->create('SearchIndex', ['url' => ['action' => 'index']]) ?>
<?php echo $this->BcForm->hidden('SearchIndex.open', ['value' => true]) ?>
<p>
	<span><?php echo $this->BcForm->label('SearchIndex.type', __d('baser', 'タイプ')) ?><?php echo $this->BcForm->input('SearchIndex.type', ['type' => 'select', 'options' => $types, 'empty' => __d('baser', '指定なし')]) ?></span>
	<span><?php echo $this->BcForm->label('SearchIndex.site_id', __d('baser', 'サブサイト')) ?><?php echo $this->BcForm->input('SearchIndex.site_id', ['type' => 'select', 'options' => $sites]) ?></span>
	<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'vertical-align:middle;display:none', 'id' => 'SearchIndexSiteIdLoader']) ?>
	<span><?php echo $this->BcForm->label('SearchIndex.folder_id', __d('baser', 'フォルダ')) ?><?php echo $this->BcForm->input('SearchIndex.folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定なし')]) ?></span>
	<span><?php echo $this->BcForm->label('SearchIndex.keyword', __d('baser', 'キーワード')) ?><?php echo $this->BcForm->input('SearchIndex.keyword', ['type' => 'text', 'size' => '30']) ?></span>
	<span><?php echo $this->BcForm->label('SearchIndex.status', __d('baser', '公開状態')) ?>
		<?php echo $this->BcForm->input('SearchIndex.status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?></span>　
	<span><?php echo $this->BcForm->label('SearchIndex.priority', __d('baser', '優先度')) ?>
		<?php echo $this->BcForm->input('SearchIndex.priority', ['type' => 'select', 'options' => $priorities, 'empty' => __d('baser', '指定なし')]) ?></span>
</p>
<div class="submit">
	<?php echo $this->BcForm->button(__d('baser', '検索'), ['class' => 'button', 'id' => 'BtnSearchSubmit']) ?>
	<?php echo $this->BcForm->button(__d('baser', 'クリア'), ['class' => 'button', 'id' => 'BtnSearchClear']) ?>
</div>
<?php echo $this->BcForm->end() ?>
