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
 * 関連コンテンツ
 * @var string $mainSiteDisplayName メインサイト表示名称
 * @var array $relatedContents 関連コンテンツ
 * @var array $sites サイトリスト
 * @var int $currentSiteId 現在のサイトID
 */

if(empty($this->request->data['Content']['url'])) {
	return;
}
$pureUrl = $this->BcContents->getPureUrl($this->request->data['Content']['url'], $this->request->data['Site']['id']);
?>


<?php if (count($relatedContents) > 1): ?>
	<section id="RelatedContentsSetting" class="bca-section" data-bca-section-type="form-group">
		<div class="bca-collapse__action">
			<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
					data-bca-target="#formRelatedContentsBody" aria-expanded="false"
					aria-controls="formOptionBody"><?php echo __d('baser', '関連コンテンツ') ?>&nbsp;&nbsp;<i
					class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
		</div>
		<div class="bca-collapse" id="formRelatedContentsBody" data-bca-state="">
			<table class="list-table bca-table-listup"
			">
			<thead class="bca-table-listup__thead">
			<tr>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'サイト名') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'メインサイト') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'タイトル') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'エイリアス') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
			</tr>
			</thead>
			<tbody class="bca-table-listup__tbody">
			<?php foreach($relatedContents as $relatedContent): ?>
				<?php
				$class = $editUrl = $checkUrl = '';
				$current = false;
				if (!empty($relatedContent['Content'])) {
					if (!$relatedContent['Content']['alias_id']) {
						$editUrl = $this->BcContents->settings[$relatedContent['Content']['type']]['url']['edit'];
						if ($relatedContent['Content']['entity_id']) {
							$editUrl .= '/' . $relatedContent['Content']['entity_id'];
						}
						$editUrl .= '/content_id:' . $relatedContent['Content']['id'] . '#RelatedContentsSetting';
					} else {
						$editUrl = '/' . BcUtil::getAdminPrefix() . '/contents/edit_alias/' . $relatedContent['Content']['id'] . '#RelatedContentsSetting';
					}
					if ($this->request->data['Content']['id'] == $relatedContent['Content']['id']) {
						$current = true;
						$class = ' class="bca-currentrow"';
					}
				} else {
					$class = ' class="bca-disablerow"';
				}
				$prefix = $relatedContent['Site']['name'];
				if ($relatedContent['Site']['alias']) {
					$prefix = $relatedContent['Site']['alias'];
				}
				$targetUrl = '/' . $prefix . $pureUrl;
				$mainSiteId = $relatedContent['Site']['main_site_id'];
				if ($mainSiteId === '') {
					$mainSiteId = 0;
				}
				?>
				<tr<?php echo $class ?> id="Row<?php echo $relatedContent['Site']['id'] ?>">
					<td class="cel2 bca-table-listup__tbody-td"><?php echo h($relatedContent['Site']['display_name']) ?></td>
					<td class="cel3 bca-table-listup__tbody-td">
						<?php echo h($this->BcText->arrayValue($relatedContent['Site']['main_site_id'], $sites, $mainSiteDisplayName)) ?>
					</td>
					<td class="cel4 bca-table-listup__tbody-td">
						<?php if (!empty($relatedContent['Content'])): ?>
							<?php echo h($relatedContent['Content']['title']) ?>
							<?php if (!empty($relatedContent['Content'])): ?>
								<small>（<?php echo h($this->BcContents->settings[$relatedContent['Content']['type']]['title']) ?>
									）</small>
							<?php endif ?>
						<?php else: ?>
							<small><?php echo __d('baser', '未登録') ?></small>
						<?php endif ?>
					</td>
					<td class="cel5 bca-table-listup__tbody-td">
						<?php if (!empty($relatedContent['Content']) && !empty($relatedContent['Content']['alias_id'])): ?>
							<i class="fa fa-check-square" aria-hidden="true"></i>
						<?php endif ?>
					</td>
					<td class="cel1 bca-table-listup__tbody-td">
						<?php if (!$current): ?>
							<?php if (!empty($relatedContent['Content'])): ?>
								<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', ['alt' => __d('baser', '確認')]), $relatedContent['Content']['url'], ['title' => __d('baser', '確認'), 'target' => '_blank']) ?>
								<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集')]), $editUrl, ['title' => __d('baser', '編集')]) ?>
							<?php elseif ($currentSiteId == $mainSiteId && $this->BcForm->value('Content.type') !== 'ContentFolder'): ?>
								<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_alias.png', ['alt' => __d('baser', 'エイリアス作成')]) . '<span class="icon-add-layerd"></span>', 'javascript:void(0)', ['class' => 'create-alias', 'title' => __d('baser', 'エイリアス作成'), 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id']]) ?>
								<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー作成')]) . '<span class="icon-add-layerd"></span>', 'javascript:void(0)', ['class' => 'create-copy', 'title' => __d('baser', 'コピー作成'), 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id']]) ?>
							<?php endif ?>
						<?php endif ?>
						<?php echo $this->BcForm->input('Site.display_name' . $relatedContent['Site']['id'], ['type' => 'hidden', 'value' => $relatedContent['Site']['display_name']]) ?>
						<?php echo $this->BcForm->input('Site.target_url' . $relatedContent['Site']['id'], ['type' => 'hidden', 'value' => $targetUrl]) ?>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
			</table>
		</div>
	</section>
<?php endif ?>
