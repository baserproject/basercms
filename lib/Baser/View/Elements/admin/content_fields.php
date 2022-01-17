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
 * [ADMIN] 統合コンテンツフォーム
 *
 * @var BcAppView $this
 */
$isOmitViewAction = $this->BcContents->settings[$this->request->data['Content']['type']]['omitViewAction'];
if ($this->request->data['Content']['url'] == '/') {
	$urlArray = [];
} else {
	$urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $this->request->data['Content']['url']));
}

// 同一URL
if ($this->request->data['Site']['same_main_url']) {
	$site = BcSite::findById($this->request->data['Site']['main_site_id']);
	array_shift($urlArray);
	if ($site->alias) {
		$urlArray = explode('/', $site->alias) + $urlArray;
	}
}
// サブドメイン
if ($this->request->data['Site']['use_subdomain']) {
	if ($urlArray) {
		$hostUrl = '/' . $urlArray[0] . '/';
	} else {
		$hostUrl = '/';
	}
	$hostUrl = $this->BcContents->getUrl($hostUrl, true, true);
	$contentsName = '';
	if (!$this->request->data['Content']['site_root']) {
		$contentsName = $this->BcForm->value('Content.name');
		if (!$isOmitViewAction && $this->request->data['Content']['url'] != '/') {
			$contentsName .= '/';
		}
	}
} else {
	if ($this->request->data['Site']['same_main_url'] && $this->request->data['Content']['site_root']) {
		$contentsName = '';
	} else {
		$contentsName = $this->BcForm->value('Content.name');
	}
	if (!$isOmitViewAction && $this->request->data['Content']['url'] != '/' && $contentsName) {
		$contentsName .= '/';
	}
	$hostUrl = $this->BcContents->getUrl('/', true, false);
}

$checkUrl = '/';
$Content = ClassRegistry::init('Content');
foreach($urlArray as $key => $value) {
	$checkUrl .= $value . '/';
	$entityId = $Content->field('entity_id', ['Content.url' => $checkUrl]);
	$urlArray[$key] = $this->BcBaser->getLink(urldecode($value), ['admin' => true, 'plugin' => '', 'controller' => 'content_folders', 'action' => 'edit', $entityId], ['forceTitle' => true]);
}
if ($urlArray && $this->request->data['Site']['use_subdomain']) {
	array_shift($urlArray);
}
unset($urlArray[count($urlArray) - 1]);
$baseUrl = '';
if ($urlArray) {
	$baseUrl = implode('/', $urlArray) . '/';
}

$baseUrl = $hostUrl . $baseUrl;

if ($this->request->data['Site']['use_subdomain']) {
	$targetSite = BcSite::findByUrl($this->request->data['Content']['url']);
	$previewUrl = $this->BcBaser->getUrl($targetSite->getPureUrl($this->request->data['Content']['url']) . '?host=' . $targetSite->host);
} else {
	$previewUrl = $this->BcBaser->getUrl($this->BcContents->getUrl($this->request->data['Content']['url'], false, false, false));
}

$pureUrl = $this->BcContents->getPureUrl($this->request->data['Content']['url'], $this->request->data['Site']['id']);
$this->BcBaser->i18nScript([
	'contentsEditConfirmMessage1' => __d('baser', 'コンテンツをゴミ箱に移動してもよろしいですか？'),
	'contentsEditConfirmMessage2' => __d('baser', "エイリアスを削除してもよろしいですか？\nエイリアスはゴミ箱に入らず完全に削除されます。"),
	'contentsEditConfirmMessage3' => __d('baser', 'このコンテンツを元に %s にエイリアスを作成します。よろしいですか？'),
	'contentsEditConfirmMessage4' => __d('baser', 'このコンテンツを元に %s にコピーを作成します。よろしいですか？'),
	'contentsEditInfoMessage1' => __d('baser', 'エイリアスを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。'),
	'contentsEditInfoMessage2' => __d('baser', 'コピーを作成しました。作成先の編集画面に移動しますのでしばらくお待ち下さい。'),
	'contentsEditAlertMessage1' => __d('baser', 'エイリアスの作成に失敗しました。'),
	'contentsEditAlertMessage2' => __d('baser', '指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。エイリアスの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。'),
	'contentsEditAlertMessage3' => __d('baser', '指定したサイトの同じ階層上にフォルダではない同名のコンテンツが存在します。コピーの作成を実行する前に、指定したサイト上の同名コンテンツを確認し名称を変更してください。'),
	'contentsEditAlertmessage4' => __d('baser', 'コピーの作成に失敗しました。')
]);
$this->BcBaser->js('admin/contents/edit', false, ['id' => 'AdminContentsEditScript',
	'data-previewurl' => $previewUrl,
	'data-fullurl' => $this->BcContents->getUrl($this->request->data['Content']['url'], true, $this->request->data['Site']['use_subdomain']),
	'data-current' => json_encode($this->request->data),
	'data-settings' => $this->BcContents->getJsonSettings()
]);
$currentSiteId = $siteId = $this->request->data['Site']['id'];
if (is_null($currentSiteId)) {
	$currentSiteId = 0;
}
$editable = $this->BcContents->isEditable();
?>


<?php echo $this->BcForm->hidden('Content.id') ?>
<?php echo $this->BcForm->hidden('Content.plugin') ?>
<?php echo $this->BcForm->hidden('Content.type') ?>
<?php echo $this->BcForm->hidden('Content.entity_id') ?>
<?php echo $this->BcForm->hidden('Content.url') ?>
<?php echo $this->BcForm->hidden('Content.alias_id') ?>
<?php echo $this->BcForm->hidden('Content.site_root') ?>
<?php echo $this->BcForm->hidden('Content.site_id') ?>
<?php echo $this->BcForm->hidden('Content.lft') ?>
<?php echo $this->BcForm->hidden('Content.rght') ?>
<?php echo $this->BcForm->hidden('Content.status') ?>
<?php echo $this->BcForm->hidden('Content.main_site_content_id') ?>


<div id="ContentsFormTabs">
	<ul>
		<li><a href="#BasicSetting"><?php echo __d('baser', '基本設定') ?></a></li>
		<li><a href="#OptionalSetting"><?php echo __d('baser', 'オプション') ?></a></li>
		<?php if (count($relatedContents) > 1): ?>
			<li><a href="#RelatedContentsSetting"><?php echo __d('baser', '関連コンテンツ') ?></a></li>
		<?php endif ?>
		<li><a href="#EtcSetting"><?php echo __d('baser', 'その他情報') ?></a></li>
	</ul>
	<div id="BasicSetting">
		<table class="form-table">
			<tr>
				<th><?php echo $this->BcForm->label('Content.name', 'URL') ?>&nbsp;<span class="required">*</span></th>
				<td>
					<smalL>[<?php echo __d('baser', 'サイト') ?>
						]</smalL> <?php echo h($this->BcText->noValue($this->request->data['Site']['display_name'], $mainSiteDisplayName)) ?>
					　
					<?php if (!$this->request->data['Content']['site_root']): ?>
						<small>[<?php echo __d('baser', 'フォルダ') ?>]</small>
						<?php echo $this->BcForm->input('Content.parent_id', ['type' => 'select', 'options' => $parentContents, 'escape' => true]) ?>　
						<?php echo $this->BcForm->error('Content.parent_id') ?>　
						<br/>
					<?php endif ?>
					<span class="url">
						<?php if (!$this->request->data['Content']['site_root'] && !$related): ?>
							<?php echo $baseUrl ?><?php echo $this->BcForm->input('Content.name', ['type' => 'text', 'size' => 20, 'autofocus' => true]) ?><?php if (!$isOmitViewAction && $this->request->data['Content']['url'] != '/'): ?>/<?php endif ?>　<?php echo $this->BcForm->button(__d('baser', 'URLコピー'), ['id' => 'BtnCopyUrl', 'class' => 'small-button', 'style' => 'font-weight:normal']) ?>
							<?php echo $this->BcForm->error('Content.name') ?>
						<?php else: ?>
							<?php echo $baseUrl ?><?php echo $contentsName ?>　<?php echo $this->BcForm->button(__d('baser', 'URLコピー'), ['id' => 'BtnCopyUrl', 'class' => 'small-button', 'style' => 'font-weight:normal']) ?>
							<?php echo $this->BcForm->hidden('Content.name') ?>
						<?php endif ?>
					</span>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo $this->BcForm->label('Content.title', __d('baser', 'タイトル')) ?>&nbsp;<span
						class="required">*</span></th>
				<td>
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.title', ['size' => 50]) ?>　
						<?php echo $this->BcForm->error('Content.title') ?>
					<?php else: ?>
						<?php echo h($this->BcForm->value('Content.title')) ?>　
						<?php echo $this->BcForm->hidden('Content.title') ?>
					<?php endif ?>
					<small>[<?php echo __d('baser', 'タイプ') ?>]</small>
					<?php if (!$this->BcForm->value('Content.alias_id')): ?>
						<?php if (!empty($this->BcContents->settings[$this->BcForm->value('Content.type')])): ?>
							<?php echo $this->BcContents->settings[$this->BcForm->value('Content.type')]['title'] ?>
						<?php else: ?>
							<?php echo __d('baser', 'デフォルト') ?>
						<?php endif ?>
					<?php else: ?>
						<?php echo __d('baser', 'エイリアス') ?>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Content.self_status', __d('baser', '公開状態')) ?></th>
				<td class="col-input">
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.self_status', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(__d('baser', '公開'))]) ?>
					<?php else: ?>
						<?php echo $this->BcText->arrayValue($this->BcForm->value('Content.self_status'), $this->BcText->booleanDoList(__d('baser', '公開'))) ?>
						<?php echo $this->BcForm->hidden('Content.self_status') ?>
					<?php endif ?>
					&nbsp;&nbsp;&nbsp;&nbsp;<small>[<?php echo __d('baser', '公開期間') ?>]</small>&nbsp;
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.self_publish_begin', [
							'type' => 'dateTimePicker',
							'size' => 12,
							'maxlength' => 10,
							'dateLabel' => ['text' => '開始日付'],
							'timeLabel' => ['text' => '開始時間']
						]) ?>
						&nbsp;〜&nbsp;
						<?php echo $this->BcForm->input('Content.self_publish_end', [
							'type' => 'dateTimePicker',
							'size' => 12,
							'maxlength' => 10,
							'dateLabel' => ['text' => '終了日付'],
							'timeLabel' => ['text' => '終了時間']
						]) ?>
					<?php else: ?>
						<?php if ($this->BcForm->value('Content.self_publish_begin') || $this->BcForm->value('Content.self_publish_end')): ?>
							<?php echo $this->BcForm->value('Content.self_publish_begin') ?>&nbsp;〜&nbsp;<?php echo $this->BcForm->value('Content.self_publish_end') ?>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.self_publish_begin') ?>
						<?php echo $this->BcForm->hidden('Content.self_publish_end') ?>
					<?php endif ?>
					<br/>
					<?php echo $this->BcForm->error('Content.self_status') ?>
					<?php echo $this->BcForm->error('Content.self_publish_begin') ?>
					<?php echo $this->BcForm->error('Content.self_publish_end') ?>
					<?php if ((bool)$this->BcForm->value('Content.status') != (bool)$this->BcForm->value('Content.self_status')): ?>
						<p class="parents-disable">※ <?php echo __d('baser', '親フォルダの設定を継承し非公開状態となっています') ?></p>
					<?php endif ?>
					<?php if (($this->BcForm->value('Content.publish_begin') != $this->BcForm->value('Content.self_publish_begin')) ||
						($this->BcForm->value('Content.publish_end') != $this->BcForm->value('Content.self_publish_end'))): ?>
						<p>※ <?php echo __d('baser', '親フォルダの設定を継承し公開期間が設定されている状態となっています') ?><br>
							（<?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.publish_begin')) ?>
							〜
							<?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.publish_end')) ?>
							）</p>
					<?php endif ?>
				</td>
			</tr>
		</table>
	</div>
	<div id="OptionalSetting">
		<table class="form-table">
			<tr>
				<th><?php echo $this->BcForm->label('Content.description', __d('baser', '説明文')) ?></th>
				<td>
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.description', ['type' => 'textarea', 'rows' => 2]) ?>　
					<?php else: ?>
						<?php if ($this->BcForm->value('Content.exclude_search')): ?>
							<?php echo $this->BcForm->value('Content.description') ?>
						<?php else: ?>
							<?php echo $this->BcBaser->siteConfig['description'] ?>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.description') ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.description') ?>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('Content.eyecatch', __d('baser', 'アイキャッチ')) ?></th>
				<td>
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.eyecatch', ['type' => 'file', 'imgsize' => 'thumb']) ?>
					<?php else: ?>
						<?php echo $this->BcUpload->uploadImage('Content.eyecatch', $this->BcForm->value('Content.eyecatch'), ['imgsize' => 'thumb']) ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.eyecatch') ?>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('Content.author_id', __d('baser', '作成者')) ?></th>
				<td>
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.author_id', ['type' => 'select', 'options' => $authors]) ?>
						<br>
						<small>[<?php echo __d('baser', '作成日') ?>
							]</small> <?php echo $this->BcForm->input('Content.created_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10]) ?>　
						<small>[<?php echo __d('baser', '更新日') ?>
							]</small> <?php echo $this->BcForm->input('Content.modified_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10]) ?>
					<?php else: ?>
						<?php echo $this->BcText->arrayValue($this->BcForm->value('Content.author_id'), $authors) ?>　

						<small>[<?php echo __d('baser', '作成日') ?>
							]</small> <?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.created_date')) ?>　
						<small>[<?php echo __d('baser', '更新日') ?>
							]</small> <?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.modified_date')) ?>
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
				<th><?php echo $this->BcForm->label('Content.layout_template', __d('baser', 'レイアウトテンプレート')) ?></th>
				<td>
					<?php echo $this->BcForm->input('Content.layout_template', ['type' => 'select', 'options' => $layoutTemplates]) ?>
					　
					<?php echo $this->BcForm->error('Content.layout_template') ?>　
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Content.exclude_search', __d('baser', 'その他設定')) ?></th>
				<td class="col-input">
					<?php if ($editable): ?>
						<span
							style="white-space: nowrap"><?php echo $this->BcForm->input('Content.exclude_search', ['type' => 'checkbox', 'label' => __d('baser', 'サイト内検索の検索結果より除外する')]) ?></span>　
						<span
							style="white-space: nowrap"><?php echo $this->BcForm->input('Content.exclude_menu', ['type' => 'checkbox', 'label' => __d('baser', '公開ページのメニューより除外する')]) ?></span>　
						<span
							style="white-space: nowrap"><?php echo $this->BcForm->input('Content.blank_link', ['type' => 'checkbox', 'label' => __d('baser', 'メニューのリンクを別ウィンドウ開く')]) ?></span>
					<?php else: ?>
						<?php if ($this->BcForm->value('Content.exclude_search')): ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外する') ?></span>　
						<?php else: ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外しない') ?></span>　
						<?php endif ?>
						<?php if ($this->BcForm->value('Content.exclude_menu')): ?>
							<span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外する') ?></span>　
						<?php else: ?>
							<span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外しない') ?></span>　
						<?php endif ?>
						<?php if ($this->BcForm->value('Content.blank_link')): ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを別ウィンドウ開く') ?></span>
						<?php else: ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを同じウィンドウに開く') ?></span>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.exclude_search') ?>
						<?php echo $this->BcForm->hidden('Content.exclude_menu') ?>
						<?php echo $this->BcForm->hidden('Content.blank_link') ?>
					<?php endif ?>
				</td>
			</tr>
		</table>
	</div>
	<?php if (count($relatedContents) > 1): ?>
		<div id="RelatedContentsSetting">
			<table class="list-table">
				<tr>
					<th style="width:170px" class="list-tool">&nbsp;</th>
					<th><?php echo __d('baser', 'サイト名') ?></th>
					<th><?php echo __d('baser', 'メインサイト') ?></th>
					<th><?php echo __d('baser', 'タイトル') ?></th>
					<th><?php echo __d('baser', 'エイリアス') ?></th>
				</tr>
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
							$class = ' class="currentrow"';
						}
					} else {
						$class = ' class="disablerow"';
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
						<td style="width:10%;white-space: nowrap">
							<?php if (!$current): ?>
								<?php if (!empty($relatedContent['Content'])): ?>
									<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', ['alt' => __d('baser', '確認')]), $relatedContent['Content']['url'], ['title' => __d('baser', '確認'), 'target' => '_blank']) ?>
									<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集')]), $editUrl, ['title' => __d('baser', '編集')]) ?>
								<?php elseif ($currentSiteId == $mainSiteId && $this->BcForm->value('Content.type') != 'ContentFolder'): ?>
									<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_alias.png', ['alt' => __d('baser', 'エイリアス作成')]) . '<span class="icon-add-layerd"></span>', 'javascript:void(0)', ['class' => 'create-alias', 'title' => __d('baser', 'エイリアス作成'), 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id']]) ?>
									<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', ['alt' => __d('baser', 'コピー作成')]) . '<span class="icon-add-layerd"></span>', 'javascript:void(0)', ['class' => 'create-copy', 'title' => __d('baser', 'コピー作成'), 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id']]) ?>
								<?php endif ?>
							<?php endif ?>
							<?php echo $this->BcForm->input('Site.display_name' . $relatedContent['Site']['id'], ['type' => 'hidden', 'value' => $relatedContent['Site']['display_name']]) ?>
							<?php echo $this->BcForm->input('Site.target_url' . $relatedContent['Site']['id'], ['type' => 'hidden', 'value' => $targetUrl]) ?>
						</td>
						<td style="width:15%"><?php echo $relatedContent['Site']['display_name'] ?></td>
						<td style="width:15%">
							<?php echo $this->BcText->arrayValue($relatedContent['Site']['main_site_id'], $sites, $mainSiteDisplayName) ?>
						</td>
						<td>
							<?php if (!empty($relatedContent['Content'])): ?>
								<?php echo h($relatedContent['Content']['title']) ?>
								<?php if (!empty($relatedContent['Content'])): ?>
									<small>（<?php echo $this->BcContents->settings[$relatedContent['Content']['type']]['title'] ?>
										）</small>
								<?php endif ?>
							<?php else: ?>
								<small><?php echo __d('baser', '未登録') ?></small>
							<?php endif ?>
						</td>
						<td style="text-align:center;width:5%">
							<?php if (!empty($relatedContent['Content']) && !empty($relatedContent['Content']['alias_id'])): ?>
								◯
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		</div>
	<?php endif ?>
	<?php if ($this->request->action == 'admin_edit' || $this->request->action == 'admin_edit_alias'): ?>
		<div id="EtcSetting">
			<div>
				<p>
					<span><?php echo __d('baser', 'コンテンツID') ?></span>：<?php echo h($this->request->data['Content']['id']) ?>
				</p>
				<p>
					<span><?php echo __d('baser', '実体ID') ?></span>：<?php echo h($this->request->data['Content']['entity_id']) ?>
				</p>
				<p>
					<span><?php echo __d('baser', 'プラグイン') ?></span>：<?php echo h($this->request->data['Content']['plugin']) ?>
				</p>
				<p>
					<span><?php echo __d('baser', 'コンテンツタイプ') ?></span>：<?php echo h($this->request->data['Content']['type']) ?>
				</p>
				<p>
					<span><?php echo __d('baser', 'データ作成日') ?></span>：<?php echo h($this->BcTime->format('Y/m/d H:i:s', $this->request->data['Content']['created'])) ?>
				</p>
				<p>
					<span><?php echo __d('baser', 'データ更新日') ?></span>：<?php echo h($this->BcTime->format('Y/m/d H:i:s', $this->request->data['Content']['modified'])) ?>
				</p>
			</div>
		</div>
	<?php endif ?>
</div>

<?php if (empty($this->BcContents->settings[$this->BcForm->value('Content.type')])): ?>
	<p class="section"><?php echo __d('baser', 'タイプ「デフォルト」は、プラグインの無効処理等が理由となり、タイプとの関連付けが外れてしまっている状態です。<br>プラグインがまだ存在する場合は有効にしてください。') ?></p>
<?php endif ?>
