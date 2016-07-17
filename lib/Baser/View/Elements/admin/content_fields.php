<?php
/**
 * [ADMIN] 統合コンテンツフォーム
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
$urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $this->request->data['Content']['url']));
unset($urlArray[count($urlArray) -1]);
$checkUrl = '/';
$Content = ClassRegistry::init('Content');
foreach($urlArray as $key => $value) {
	$checkUrl .= $value . '/';
	$entityId = $Content->field('entity_id', ['Content.url' => $checkUrl]);
	$urlArray[$key] = $this->BcBaser->getLink(urldecode($value), array('admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $entityId));
}
$baseUrl = '';
if($urlArray) {
	$baseUrl = implode('/', $urlArray) . '/';
}
$baseUrl = $this->BcContents->getUrl('/', true, $this->request->data['Site']['use_subdomain']) . $baseUrl;
$pureUrl = $this->BcContents->getPureUrl($this->request->data['Content']['url'], $this->request->data['Site']['name'], $this->request->data['Site']['alias']);
$this->BcBaser->js('admin/contents/edit', false, array('id' => 'AdminContentsEditScript',
	'data-fullurl' => $this->BcContents->getUrl($this->request->data['Content']['url'], true, $this->request->data['Site']['use_subdomain']),
	'data-current' => json_encode($this->request->data),
	'data-settings' => $this->BcContents->getJsonSettings()
));
$currentSiteId = $siteId = $this->request->data['Site']['id'];
if(is_null($currentSiteId)) {
	$currentSiteId = 0;
}
$disableEdit = false;
if($this->request->data['Site']['relate_main_site'] && $this->request->data['Content']['main_site_content_id'] && ($this->request->data['Content']['alias_id'] || $this->request->data['Content']['type'] == 'ContentFolder')) {
	$disableEdit = true;
}
?>


<?php echo $this->BcForm->hidden('Content.id') ?>
<?php echo $this->BcForm->hidden('Content.plugin') ?>
<?php echo $this->BcForm->hidden('Content.type') ?>
<?php echo $this->BcForm->hidden('Content.entity_id') ?>
<?php echo $this->BcForm->hidden('Content.parent_id') ?>
<?php echo $this->BcForm->hidden('Content.url') ?>
<?php echo $this->BcForm->hidden('Content.alias_id') ?>
<?php echo $this->BcForm->hidden('Content.site_root') ?>
<?php echo $this->BcForm->hidden('Content.site_id') ?>


<div id="ContentsFormTabs">
	<ul>
		<li><a href="#BasicSetting">基本情報</a></li>
		<li><a href="#OptionalSetting">オプション</a></li>
		<?php if(count($relatedContents) > 1): ?>
		<li><a href="#RelatedContentsSetting">関連コンテンツ</a></li>
		<?php endif ?>
	</ul>
	<div id="BasicSetting">
		<table class="form-table" >
			<tr>
				<th><?php echo $this->BcForm->label('Content.name', 'URL') ?>&nbsp;<span class="required">*</span></th>
				<td>
					<smalL>[サイト]</smalL> <?php echo $this->BcText->noValue($this->request->data['Site']['display_name'], 'HOME') ?>　
					<?php if(!$this->request->data['Content']['site_root']): ?>
					<small>[フォルダ]</small>
					<?php echo $this->BcForm->input('Content.parent_id', array('type' => 'select', 'options' => $parentContents, 'escape' => false)) ?>　
					<?php echo $this->BcForm->error('Content.parent_id') ?>　
					<br />
					<?php endif ?>
					<span class="url">
						<?php if(!$this->request->data['Content']['site_root']): ?>
							<?php echo $baseUrl ?><?php echo $this->BcForm->input('Content.name', array('type' => 'text', 'size' => 20)) ?><?php if($this->request->data['Content']['type'] == 'ContentFolder' && $this->request->data['Content']['url'] != '/'): ?>/<?php endif ?>
							<?php echo $this->BcForm->error('Content.name') ?>
						<?php else: ?>
							<?php echo $baseUrl ?><?php echo $this->BcForm->value('Content.name') ?><?php if($this->request->data['Content']['type'] == 'ContentFolder' && $this->request->data['Content']['url'] != '/'): ?>/<?php endif ?>
							<?php echo $this->BcForm->hidden('Content.name') ?>
						<?php endif ?>
					</span>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo $this->BcForm->label('Content.title', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
				<td>
					<?php if(!$this->request->data['Content']['site_root'] && !$disableEdit): ?>
						<?php echo $this->BcForm->input('Content.title', array('size' => 50)) ?>　
						<?php echo $this->BcForm->error('Content.title') ?>
					<?php else: ?>
						<?php echo $this->BcForm->value('Content.title') ?>　
						<?php echo $this->BcForm->hidden('Content.title') ?>
					<?php endif ?>
					<small>[タイプ]</small>
					<?php if(!$this->BcForm->value('Content.alias_id')): ?>
					<?php echo $contentsSettings[$this->BcForm->value('Content.type')]['title'] ?>
					<?php else: ?>
					エイリアス
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Content.status', '公開状態') ?>&nbsp;<span class="required">*</span></th>
				<td class="col-input">
					<?php if($isPublishByParents && !$this->request->data['Content']['site_root'] && !$disableEdit): ?>
						<?php echo $this->BcForm->input('Content.status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('公開'))) ?>
					<?php else: ?>
						<?php echo $this->BcText->arrayValue($this->BcForm->value('Content.status'), $this->BcText->booleanDoList('公開')) ?>
						<?php echo $this->BcForm->hidden('Content.status') ?>
					<?php endif ?>
					&nbsp;&nbsp;
					<?php if($isPublishByParents && !$this->request->data['Content']['site_root'] && !$disableEdit): ?>
						<?php echo $this->BcForm->dateTimePicker('Content.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
						&nbsp;〜&nbsp;
						<?php echo $this->BcForm->dateTimePicker('Content.publish_end', array('size' => 12, 'maxlength' => 10), true) ?>
					<?php else: ?>
						<?php if($this->BcForm->value('Content.publish_begin') || $this->BcForm->value('Content.publish_end')): ?>
							<?php echo $this->BcForm->value('Content.publish_begin') ?>&nbsp;〜&nbsp;<?php echo $this->BcForm->value('Content.publish_end') ?>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.publish_begin') ?>
						<?php echo $this->BcForm->hidden('Content.publish_end') ?>
					<?php endif ?>
					<br />
					<?php echo $this->BcForm->error('Content.status') ?>
					<?php echo $this->BcForm->error('Content.publish_begin') ?>
					<?php echo $this->BcForm->error('Content.publish_end') ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="OptionalSetting">
		<table class="form-table" >
			<tr>
				<th><?php echo $this->BcForm->label('Content.description', '説明文') ?></th>
				<td>
					<?php if(!$disableEdit): ?>
						<?php echo $this->BcForm->input('Content.description', array('type' => 'textarea', 'rows' => 2)) ?>　
					<?php else: ?>
						<?php echo $this->BcForm->value('Content.description') ?>
						<?php echo $this->BcForm->hidden('Content.description') ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.description') ?>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('Content.eyecatch', 'アイキャッチ') ?></th>
				<td>
					<?php if(!$disableEdit): ?>
						<?php echo $this->BcForm->file('Content.eyecatch', ['imgsize' => 'thumb']) ?>
					<?php else: ?>
						<?php echo $this->BcUpload->uploadImage('Content.eyecatch', $this->BcForm->value('Content.eyecatch'), ['imgsize' => 'thumb']) ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.eyecatch') ?>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('Content.author_id', '作成者') ?></th>
				<td>
					<?php if(!$disableEdit): ?>
					<?php echo $this->BcForm->input('Content.author_id', array('type' => 'select', 'options' => $authors)) ?>　
					<small>[作成日]</small> <?php echo $this->BcForm->dateTimePicker('Content.created_date', array('size' => 12, 'maxlength' => 10), true) ?>　
					<small>[更新日]</small> <?php echo $this->BcForm->dateTimePicker('Content.modified_date', array('size' => 12, 'maxlength' => 10), true) ?>
					<?php else: ?>
						<?php echo $this->BcText->arrayValue($this->BcForm->value('Content.author_id'), $authors) ?>　

					<small>[作成日]</small> <?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.created_date')) ?>　
					<small>[更新日]</small> <?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.modified_date')) ?>
						<?php echo $this->BcForm->hidden('Content.author_id') ?>
						<?php echo $this->BcForm->hidden('Content.created_date') ?>
						<?php echo $this->BcForm->hidden('Content.modified_date') ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.author_id') ?>
					<?php echo $this->BcForm->error('Content.created_date') ?>
					<?php echo $this->BcForm->error('Content.modified_date') ?>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('Content.layout_template', 'レイアウトテンプレート') ?></th>
				<td>
					<?php echo $this->BcForm->input('Content.layout_template', array('type' => 'select', 'options' => $layoutTemplates)) ?>　
					<?php echo $this->BcForm->error('Content.layout_template') ?>　
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Content.exclude_search', 'その他設定') ?></th>
				<td class="col-input">
					<?php if(!$disableEdit): ?>
					<?php echo $this->BcForm->input('Content.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?>
					<?php else: ?>
						<?php if($this->BcForm->value('Content.exclude_search')): ?>
							サイト内検索の検索結果より除外する
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.modified_date') ?>
					<?php endif ?>
				</td>
			</tr>
		</table>
	</div>
<?php if(count($relatedContents) > 1): ?>
	<div id="RelatedContentsSetting">
		<table class="list-table">
			<tr>
				<th style="width:170px" class="list-tool">&nbsp;</th>
				<th>サイト名</th>
				<th>メインサイト</th>
				<th>タイトル</th>
				<th>エイリアス</th>
			</tr>
			<?php foreach($relatedContents as $relatedContent): ?>
				<?php
					$class = $editUrl = $checkUrl = '';
					$current = false;
					if(!empty($relatedContent['Content'])){
						if(!$relatedContent['Content']['alias_id']) {
							$editUrl = $contentsSettings[$relatedContent['Content']['type']]['routes']['edit'];
							if($relatedContent['Content']['entity_id']) {
								$editUrl .= '/' . $relatedContent['Content']['entity_id'];
							}
							$editUrl .= '/content_id:' . $relatedContent['Content']['id'] . '#RelatedContentsSetting';
						} else {
							$editUrl = '/admin/contents/edit_alias/' . $relatedContent['Content']['id'] . '#RelatedContentsSetting';
						}
						if($this->request->data['Content']['id'] == $relatedContent['Content']['id']) {
							$current = true;
							$class = ' class="currentrow"';
						}
					} else {
						$class = ' class="disablerow"';
					}
					$prefix =$relatedContent['Site']['name'];
					if($relatedContent['Site']['alias']) {
						$prefix = $relatedContent['Site']['alias'];
					}
					$targetUrl = '/' . $prefix . $pureUrl;
					$mainSiteId = $relatedContent['Site']['main_site_id'];
					if($mainSiteId === '') {
						$mainSiteId = 0;
					}
				?>
			<tr<?php echo $class ?> id="Row<?php echo $relatedContent['Site']['id'] ?>">
				<td style="width:10%">

					<?php if(!$current): ?>
						<?php if(!empty($relatedContent['Content'])): ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_check.png', array('alt' => '確認')), $relatedContent['Content']['url'], array('title' => '確認', 'target' => '_blank')) ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_edit.png', array('alt' => '編集')), $editUrl, array('title' => '編集')) ?>
						<?php elseif($currentSiteId == $mainSiteId && $this->BcForm->value('Content.type') != 'ContentFolder'): ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_alias.png', array('alt' => 'エイリアス作成')) . '<span class="icon-add-layerd"></span>', '', array('class' => 'create-alias', 'title' => 'エイリアス作成', 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id'])) ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_copy.png', array('alt' => 'コピー作成')) . '<span class="icon-add-layerd"></span>', '', array('class' => 'create-copy', 'title' => 'コピー作成', 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id'])) ?>
						<?php endif ?>
					<?php endif ?>
					<?php echo $this->BcForm->input('Site.display_name' . $relatedContent['Site']['id'], array('type' => 'hidden', 'value' => $relatedContent['Site']['display_name'])) ?>
					<?php echo $this->BcForm->input('Site.target_url' . $relatedContent['Site']['id'], array('type' => 'hidden', 'value' => $targetUrl)) ?>
				</td>
				<td style="width:15%"><?php echo $relatedContent['Site']['display_name'] ?></td>
				<td style="width:15%">
					<?php echo $sites[$relatedContent['Site']['main_site_id']] ?>
				</td>
				<td>
					<?php if(!empty($relatedContent['Content'])): ?>
						<?php echo $relatedContent['Content']['title'] ?>
						<?php if(!empty($relatedContent['Content'])): ?>
							<small>（<?php echo $contentsSettings[$relatedContent['Content']['type']]['title'] ?>）</small>
						<?php endif ?>
					<?php else: ?>
						<small>未登録</small>
					<?php endif ?>
				</td>
				<td style="text-align:center;width:5%">
					<?php if(!empty($relatedContent['Content']) && !empty($relatedContent['Content']['alias_id'])): ?>
						◯
					<?php endif ?>
				</td>
			</tr>
			<?php endforeach ?>
		</table>
	</div>
<?php endif ?>
</div>