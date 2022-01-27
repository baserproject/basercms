<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.0.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツフォーム
 *
 * @var BcAppView $this
 * @var array $parentContents
 * @var bool $related 親サイトに連携する設定で、エイリアス、もしくはフォルダであるかどうか
 *                                        上記に一致する場合、URLに関わるコンテンツ名は編集できない
 * @var bool $editable コンテンツ編集不可かどうか
 */

$fullUrl = $previewUrl = '';
if(!empty($this->request->data['Content']['url'])) {
	if ($this->request->data['Site']['use_subdomain']) {
		$targetSite = BcSite::findByUrl($this->request->data['Content']['url']);
		$previewUrl = $this->BcBaser->getUrl($targetSite->getPureUrl($this->request->data['Content']['url']) . '?host=' . $targetSite->host);
	} else {
		$previewUrl = $this->BcBaser->getUrl($this->BcContents->getUrl($this->request->data['Content']['url'], false, false, false));
	}
	$fullUrl = $this->BcContents->getUrl($this->request->data['Content']['url'], true, $this->request->data['Site']['use_subdomain']);
}

$this->BcBaser->js('admin/contents/edit', false, ['id' => 'AdminContentsEditScript',
	'data-previewurl' => $previewUrl,
	'data-fullurl' => $fullUrl,
	'data-current' => json_encode($this->request->data),
	'data-settings' => $this->BcContents->getJsonSettings()
]);
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
$isOmitViewAction = $this->BcContents->settings[$this->request->data['Content']['type']]['omitViewAction'];

// サブドメイン
if ($this->request->data['Site']['use_subdomain']) {
	$contentsName = '';
	if (!$this->request->data['Content']['site_root']) {
		$contentsName = $this->BcForm->value('Content.name');
		if (!$isOmitViewAction && $this->request->data['Content']['url'] !== '/') {
			$contentsName .= '/';
		}
	}
} else {
	if ($this->request->data['Site']['same_main_url'] && $this->request->data['Content']['site_root']) {
		$contentsName = '';
	} else {
		$contentsName = $this->BcForm->value('Content.name');
	}
	if (!$isOmitViewAction && $this->request->data['Content']['url'] !== '/' && $contentsName) {
		$contentsName .= '/';
	}
}
$linkedFullUrl = $this->BcContents->getCurrentFolderLinkedUrl() . $contentsName;
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


<?php if($fullUrl): ?>
<div class="bca-section bca-section__post-top">
  <span class="bca-post__url">
	  <a href="<?php echo h($fullUrl) ?>" class="bca-text-url" target="_blank" data-toggle="tooltip"
		 data-placement="top" title="<?php echo __d('baser', '公開URLを開きます') ?>"><i
			  class="bca-icon--globe"></i><?php echo urldecode($fullUrl) ?></a>
	  <?php echo $this->BcForm->button('', [
		  'id' => 'BtnCopyUrl',
		  'class' => 'bca-btn',
		  'type' => 'button',
		  'data-bca-btn-type' => 'textcopy',
		  'data-bca-btn-category' => 'text',
		  'data-bca-btn-size' => 'sm'
	  ]) ?>
</div>
<?php endif ?>


<section id="BasicSetting" class="bca-section">
	<table class="form-table bca-form-table" data-bca-table-type="type2">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.name', 'URL') ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php if (!$this->request->data['Content']['site_root']): ?>
					<?php echo $this->BcForm->input('Content.parent_id', ['type' => 'select', 'options' => $parentContents, 'escape' => true]) ?>
				<?php endif ?>
				<?php if (!$this->request->data['Content']['site_root'] && !$related): ?>
					<?php echo $this->BcForm->input('Content.name', ['type' => 'text', 'size' => 20, 'autofocus' => true]) ?>
					<?php if (!$isOmitViewAction && $this->request->data['Content']['url'] !== '/'): ?>/<?php endif ?>　
				<?php else: ?>
					<?php if (!$this->request->data['Content']['site_root']): ?>
						<?php // サイトルートの場合はコンテンツ名を表示しない ?>
						<?php echo h($contentsName) ?>
					<?php endif ?>
					<?php echo $this->BcForm->hidden('Content.name') ?>
				<?php endif ?>
				<?php echo $this->BcForm->error('Content.name') ?>
				<?php echo $this->BcForm->error('Content.parent_id') ?>
				<span class="bca-post__url">
          			<?php echo strip_tags($linkedFullUrl, '<a>') ?>
        		</span>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('Content.title', __d('baser', 'タイトル')) ?>&nbsp;<span class="bca-label"
																									 data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php if ($editable): ?>
					<?php echo $this->BcForm->input('Content.title', ['type' => 'text', 'size' => 50]) ?>　
					<?php echo $this->BcForm->error('Content.title') ?>
				<?php else: ?>
					<?php echo h($this->BcForm->value('Content.title')) ?>　
					<?php echo $this->BcForm->hidden('Content.title') ?>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.self_status', __d('baser', '公開状態')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php if ($editable): ?>
					<?php echo $this->BcForm->input('Content.self_status', ['type' => 'radio', 'options' => [0 => __d('baser', '公開しない'), 1 => __d('baser', '公開する')]]) ?>
				<?php else: ?>
					<?php echo $this->BcText->arrayValue($this->BcForm->value('Content.self_status'), [0 => __d('baser', '公開しない'), 1 => __d('baser', '公開する')]) ?>
					<?php echo $this->BcForm->hidden('Content.self_status') ?>
				<?php endif ?>
				<br>
				<?php echo $this->BcForm->error('Content.self_status') ?>
				<?php if ((bool)$this->BcForm->value('Content.status') != (bool)$this->BcForm->value('Content.self_status')): ?>
					<p>※ <?php echo __d('baser', '親フォルダの設定を継承し非公開状態となっています') ?></p>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.self_status', __d('baser', '公開日時')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php if ($editable): ?>
					<?php echo $this->BcForm->input('Content.self_publish_begin', [
						'type' => 'dateTimePicker',
						'size' => 12,
						'maxlength' => 10,
						'dateLabel' => ['text' => __d('baser', '開始日付')],
						'timeLabel' => ['text' => __d('baser', '開始時間')]
					]) ?>
					&nbsp;〜&nbsp;
					<?php echo $this->BcForm->input('Content.self_publish_end', [
						'type' => 'dateTimePicker',
						'size' => 12, 'maxlength' => 10,
						'dateLabel' => ['text' => __d('baser', '終了日付')],
						'timeLabel' => ['text' => __d('baser', '終了時間')]
					]) ?>
				<?php else: ?>
					<?php if ($this->BcForm->value('Content.self_publish_begin') || $this->BcForm->value('Content.self_publish_end')): ?>
						<?php echo $this->BcForm->value('Content.self_publish_begin') ?>&nbsp;〜&nbsp;<?php echo $this->BcForm->value('Content.self_publish_end') ?>
					<?php endif ?>
					<?php echo $this->BcForm->hidden('Content.self_publish_begin') ?>
					<?php echo $this->BcForm->hidden('Content.self_publish_end') ?>
				<?php endif ?>
				<br>
				<?php echo $this->BcForm->error('Content.self_publish_begin') ?>
				<?php echo $this->BcForm->error('Content.self_publish_end') ?>
				<?php if (($this->BcForm->value('Content.publish_begin') != $this->BcForm->value('Content.self_publish_begin')) ||
					($this->BcForm->value('Content.publish_end') != $this->BcForm->value('Content.self_publish_end'))): ?>
					<p>※ <?php echo __d('baser', '親フォルダの設定を継承し公開期間が設定されている状態となっています') ?><br>
						（<?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.publish_begin')) ?>
						〜
						<?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.publish_end')) ?>）
					</p>
				<?php endif ?>
			</td>
		</tr>
	</table>
</section>


