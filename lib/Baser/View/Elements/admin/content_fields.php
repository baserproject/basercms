<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツフォーム
 */
$isOmitViewAction = $this->BcContents->settings[$this->request->data['Content']['type']]['omitViewAction'];
if($this->request->data['Content']['url'] == '/') {
	$urlArray = [];
} else {
	$urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $this->request->data['Content']['url']));
}

// 同一URL
if($this->request->data['Site']['same_main_url']) {
	$site = BcSite::findById($this->request->data['Site']['main_site_id']);
	array_shift($urlArray);
	if($site->alias) {
		$urlArray = explode('/', $site->alias) + $urlArray;
	}
}
// サブドメイン
if($this->request->data['Site']['use_subdomain']) {
	if($urlArray) {
		$hostUrl = '/' . $urlArray[0] . '/';
	} else {
		$hostUrl = '/';
	}
	$hostUrl = $this->BcContents->getUrl($hostUrl, true, true);
	$contentsName = '';
	if(!$this->request->data['Content']['site_root']) {
		$contentsName = $this->BcForm->value('Content.name');
		if(!$isOmitViewAction && $this->request->data['Content']['url'] != '/') {
			$contentsName .= '/';
		}
	}
} else {
	if($this->request->data['Site']['same_main_url'] && $this->request->data['Content']['site_root']) {
		$contentsName = '';
	} else {
		$contentsName = $this->BcForm->value('Content.name');
	}
	if(!$isOmitViewAction && $this->request->data['Content']['url'] != '/' && $contentsName) {
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
if($urlArray && $this->request->data['Site']['use_subdomain']) {
	array_shift($urlArray);
}
unset($urlArray[count($urlArray) -1]);
$baseUrl = '';
if($urlArray) {
	$baseUrl = implode('/', $urlArray) . '/';
}

$baseUrl = $hostUrl . $baseUrl;

if($this->request->data['Site']['use_subdomain']) {
	$targetSite = BcSite::findByUrl($this->request->data['Content']['url']);
	$previewUrl = $targetSite->getPureUrl($this->request->data['Content']['url']) . '?host=' . $targetSite->host;
} else {
	$previewUrl = $this->BcContents->getUrl($this->request->data['Content']['url'], false);
}

$pureUrl = $this->BcContents->getPureUrl($this->request->data['Content']['url'], $this->request->data['Site']['id']);
$this->BcBaser->js('admin/contents/edit', false, array('id' => 'AdminContentsEditScript',
	'data-previewurl' => $previewUrl,
	'data-fullurl' => $this->BcContents->getUrl($this->request->data['Content']['url'], true, $this->request->data['Site']['use_subdomain']),
	'data-current' => json_encode($this->request->data),
	'data-settings' => $this->BcContents->getJsonSettings()
));
$currentSiteId = $siteId = $this->request->data['Site']['id'];
if(is_null($currentSiteId)) {
	$currentSiteId = 0;
}
$disableEdit = false;
if($this->BcContents->isEditable()) {
	$disableEdit = true;
}
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
		<li><a href="#BasicSetting">基本設定</a></li>
		<li><a href="#OptionalSetting">オプション</a></li>
		<?php if(count($relatedContents) > 1): ?>
		<li><a href="#RelatedContentsSetting">関連コンテンツ</a></li>
		<?php endif ?>
		<li><a href="#EtcSetting">その他情報</a></li>
	</ul>
	<div id="BasicSetting">
		<table class="form-table" >
			<tr>
				<th><?php echo $this->BcForm->label('Content.name', 'URL') ?>&nbsp;<span class="required">*</span></th>
				<td>
					<smalL>[サイト]</smalL> <?php echo $this->BcText->noValue($this->request->data['Site']['display_name'], $mainSiteDisplayName) ?>　
					<?php if(!$this->request->data['Content']['site_root']): ?>
					<small>[フォルダ]</small>
					<?php echo $this->BcForm->input('Content.parent_id', array('type' => 'select', 'options' => $parentContents, 'escape' => true)) ?>　
					<?php echo $this->BcForm->error('Content.parent_id') ?>　
					<br />
					<?php endif ?>
					<span class="url">
						<?php if(!$this->request->data['Content']['site_root'] && !$related): ?>
							<?php echo $baseUrl ?><?php echo $this->BcForm->input('Content.name', array('type' => 'text', 'size' => 20, 'autofocus' => true)) ?><?php if(!$isOmitViewAction && $this->request->data['Content']['url'] != '/'): ?>/<?php endif ?>　<?php echo $this->BcForm->button('URLコピー', ['id' => 'BtnCopyUrl', 'class' => 'small-button', 'style' => 'font-weight:normal']) ?>
							<?php echo $this->BcForm->error('Content.name') ?>
						<?php else: ?>
							<?php echo $baseUrl ?><?php echo $contentsName ?>　<?php echo $this->BcForm->button('URLコピー', ['id' => 'BtnCopyUrl', 'class' => 'small-button', 'style' => 'font-weight:normal']) ?>
							<?php echo $this->BcForm->hidden('Content.name') ?>
						<?php endif ?>
					</span>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo $this->BcForm->label('Content.title', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
				<td>
					<?php if(!$disableEdit): ?>
						<?php echo $this->BcForm->input('Content.title', array('size' => 50)) ?>　
						<?php echo $this->BcForm->error('Content.title') ?>
					<?php else: ?>
						<?php echo $this->BcForm->value('Content.title') ?>　
						<?php echo $this->BcForm->hidden('Content.title') ?>
					<?php endif ?>
					<small>[タイプ]</small>
					<?php if(!$this->BcForm->value('Content.alias_id')): ?>
						<?php if(!empty($this->BcContents->settings[$this->BcForm->value('Content.type')])): ?>
							<?php echo $this->BcContents->settings[$this->BcForm->value('Content.type')]['title'] ?>
						<?php else: ?>
							デフォルト	
						<?php endif ?>
					<?php else: ?>
					エイリアス
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Content.self_status', '公開状態') ?>&nbsp;<span class="required">*</span></th>
				<td class="col-input">
					<?php if(!$disableEdit): ?>
						<?php echo $this->BcForm->input('Content.self_status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('公開'))) ?>
					<?php else: ?>
						<?php echo $this->BcText->arrayValue($this->BcForm->value('Content.self_status'), $this->BcText->booleanDoList('公開')) ?>
						<?php echo $this->BcForm->hidden('Content.self_status') ?>
					<?php endif ?>
					&nbsp;&nbsp;
					<?php if(!$disableEdit): ?>
						<?php echo $this->BcForm->dateTimePicker('Content.self_publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
						&nbsp;〜&nbsp;
						<?php echo $this->BcForm->dateTimePicker('Content.self_publish_end', array('size' => 12, 'maxlength' => 10), true) ?>
					<?php else: ?>
						<?php if($this->BcForm->value('Content.self_publish_begin') || $this->BcForm->value('Content.self_publish_end')): ?>
							<?php echo $this->BcForm->value('Content.self_publish_begin') ?>&nbsp;〜&nbsp;<?php echo $this->BcForm->value('Content.self_publish_end') ?>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.self_publish_begin') ?>
						<?php echo $this->BcForm->hidden('Content.self_publish_end') ?>
					<?php endif ?>
					<br />
					<?php echo $this->BcForm->error('Content.self_status') ?>
					<?php echo $this->BcForm->error('Content.self_publish_begin') ?>
					<?php echo $this->BcForm->error('Content.self_publish_end') ?>
					<?php if((bool) $this->BcForm->value('Content.status') != (bool) $this->BcForm->value('Content.self_status')): ?>
						<p class="parents-disable">※ 親フォルダの設定を継承し非公開状態となっています</p>
					<?php endif ?>
					<?php if(($this->BcForm->value('Content.publish_begin') != $this->BcForm->value('Content.self_publish_begin')) || 
							($this->BcForm->value('Content.publish_end') != $this->BcForm->value('Content.self_publish_end'))): ?>
						<p>※ 親フォルダの設定を継承し公開期間が設定されている状態となっています<br>
							（<?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.publish_begin')) ?> 〜
							<?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.publish_end')) ?>）</p>
					<?php endif ?>
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
						<?php echo $this->BcForm->input('Content.description', array('type' => 'textarea', 'rows' => 2, 'placeholder' => $this->BcBaser->siteConfig['description'])) ?>　
					<?php else: ?>
						<?php if($this->BcForm->value('Content.exclude_search')): ?>
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
					<?php echo $this->BcForm->input('Content.author_id', array('type' => 'select', 'options' => $authors)) ?><br>
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
						<span style="white-space: nowrap"><?php echo $this->BcForm->input('Content.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?></span>　
						<span style="white-space: nowrap"><?php echo $this->BcForm->input('Content.exclude_menu', array('type' => 'checkbox', 'label' => '公開ページのメニューより除外する')) ?></span>　
						<span style="white-space: nowrap"><?php echo $this->BcForm->input('Content.blank_link', array('type' => 'checkbox', 'label' => 'メニューのリンクを別ウィンドウ開く')) ?></span>
					<?php else: ?>
						<?php if($this->BcForm->value('Content.exclude_search')): ?>
							<span style="white-space: nowrap">サイト内検索の検索結果より除外する</span>　
						<?php else: ?>
							<span style="white-space: nowrap">サイト内検索の検索結果より除外しない</span>　
						<?php endif ?>
						<?php if($this->BcForm->value('Content.exclude_menu')): ?>
							<span style="white-space: nowrap">公開ページのメニューより除外する</span>　
						<?php else: ?>
							<span style="white-space: nowrap">公開ページのメニューより除外しない</span>　
						<?php endif ?>
						<?php if($this->BcForm->value('Content.blank_link')): ?>
							<span style="white-space: nowrap">メニューのリンクを別ウィンドウ開く</span>
						<?php else: ?>
							<span style="white-space: nowrap">メニューのリンクを同じウィンドウに開く</span>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.exclude_search') ?>
						<?php echo $this->BcForm->hidden('Content.exclude_menu') ?>
						<?php echo $this->BcForm->hidden('Content.blank_link') ?>
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
							$editUrl = $this->BcContents->settings[$relatedContent['Content']['type']]['url']['edit'];
							if($relatedContent['Content']['entity_id']) {
								$editUrl .= '/' . $relatedContent['Content']['entity_id'];
							}
							$editUrl .= '/content_id:' . $relatedContent['Content']['id'] . '#RelatedContentsSetting';
						} else {
							$editUrl = '/' . BcUtil::getAdminPrefix() . '/contents/edit_alias/' . $relatedContent['Content']['id'] . '#RelatedContentsSetting';
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
				<td style="width:10%;white-space: nowrap">
					<?php if(!$current): ?>
						<?php if(!empty($relatedContent['Content'])): ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('alt' => '確認')), $relatedContent['Content']['url'], array('title' => '確認', 'target' => '_blank')) ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集')), $editUrl, array('title' => '編集')) ?>
						<?php elseif($currentSiteId == $mainSiteId && $this->BcForm->value('Content.type') != 'ContentFolder'): ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icon_alias.png', array('alt' => 'エイリアス作成')) . '<span class="icon-add-layerd"></span>', 'javascript:void(0)', array('class' => 'create-alias', 'title' => 'エイリアス作成', 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id'])) ?>
							<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('alt' => 'コピー作成')) . '<span class="icon-add-layerd"></span>', 'javascript:void(0)', array('class' => 'create-copy', 'title' => 'コピー作成', 'target' => '_blank', 'data-site-id' => $relatedContent['Site']['id'])) ?>
						<?php endif ?>
					<?php endif ?>
					<?php echo $this->BcForm->input('Site.display_name' . $relatedContent['Site']['id'], array('type' => 'hidden', 'value' => $relatedContent['Site']['display_name'])) ?>
					<?php echo $this->BcForm->input('Site.target_url' . $relatedContent['Site']['id'], array('type' => 'hidden', 'value' => $targetUrl)) ?>
				</td>
				<td style="width:15%"><?php echo $relatedContent['Site']['display_name'] ?></td>
				<td style="width:15%">
					<?php echo $this->BcText->arrayValue($relatedContent['Site']['main_site_id'], $sites,  $mainSiteDisplayName) ?>
				</td>
				<td>
					<?php if(!empty($relatedContent['Content'])): ?>
						<?php echo $relatedContent['Content']['title'] ?>
						<?php if(!empty($relatedContent['Content'])): ?>
							<small>（<?php echo $this->BcContents->settings[$relatedContent['Content']['type']]['title'] ?>）</small>
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
<?php if($this->request->action == 'admin_edit' || $this->request->action == 'admin_edit_alias'): ?>
	<div id="EtcSetting">
		<div>
		<p><span>コンテンツID</span>：<?php echo h($this->request->data['Content']['id']) ?></p>
		<p><span>実体ID</span>：<?php echo h($this->request->data['Content']['entity_id']) ?></p>
		<p><span>プラグイン</span>：<?php echo h($this->request->data['Content']['plugin']) ?></p>
		<p><span>コンテンツタイプ</span>：<?php echo h($this->request->data['Content']['type']) ?></p>
		<p><span>データ作成日</span>：<?php echo h($this->BcTime->format('Y/m/d H:i:s', $this->request->data['Content']['created'])) ?></p>
		<p><span>データ更新日</span>：<?php echo h($this->BcTime->format('Y/m/d H:i:s', $this->request->data['Content']['modified'])) ?></p>
		</div>
	</div>
<?php endif ?>
</div>

<?php if(empty($this->BcContents->settings[$this->BcForm->value('Content.type')])): ?>
<p class="section">タイプ「デフォルト」は、プラグインの無効処理等が理由となり、タイプとの関連付けが外れてしまっている状態です。<br>プラグインがまだ存在する場合は有効にしてください。</p>
<?php endif ?>