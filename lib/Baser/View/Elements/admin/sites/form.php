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
 * サブサイトフォーム
 */
$agents = Configure::read('BcAgent');
$devices = ['' => __d('baser', '指定しない')];
foreach($agents as $key => $agent) {
	$devices[$key] = $agent['name'];
}
$languages = Configure::read('BcLang');
$langs = ['' => __d('baser', '指定しない')];
foreach($languages as $key => $lang) {
	$langs[$key] = $lang['name'];
}
$useSiteDeviceSetting = 0;
$useSiteLangSetting = 0;
$thisSiteConfig = $this->get('siteConfig');
if (isset($thisSiteConfig['use_site_lang_setting'])) {
	$useSiteDeviceSetting = $thisSiteConfig['use_site_lang_setting'];
}
if (isset($thisSiteConfig['use_site_device_setting'])) {
	$useSiteLangSetting = $thisSiteConfig['use_site_device_setting'];
}
?>


<?php echo $this->BcForm->hidden('Site.id') ?>

<table class="form-table">
	<?php if ($this->request->action == 'admin_edit'): ?>
		<tr>
			<th><?php echo $this->BcForm->label('Site.id', 'NO') ?></th>
			<td>
				<?php echo $this->BcForm->value('Site.id') ?>
			</td>
		</tr>
	<?php endif ?>
	<tr>
		<th><?php echo $this->BcForm->label('Site.name', __d('baser', '識別名称')) ?>&nbsp;<span class="required">*</span>
		</th>
		<td>
			<?php echo $this->BcForm->input('Site.name', ['type' => 'input', 'size' => '30', 'autofocus' => true]) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div
				class="helptext"><?php echo __d('baser', 'サブサイトを特定する事ができる識別名称を入力します。半角英数とハイフン（-）・アンダースコア（_）のみが利用できます。エイリアスを入力しない場合は、URLにも利用されます。') ?></div>
			　<span
				style="white-space: nowrap;"><small>[<?php echo $this->BcForm->label('Site.alias', __d('baser', 'エイリアス')) ?>]</small>
			<?php echo $this->BcForm->input('Site.alias', ['type' => 'input', 'size' => '10']) ?></span>
			<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div
				class="helptext"><?php echo __d('baser', 'サブサイトの識別名称とは別のURLにしたい場合、別名を入力する事ができます。エイリアスは半角英数に加えハイフン（-）・アンダースコア（_）・スラッシュ（/）・ドット（.）が利用できます。') ?></div>
			<?php echo $this->BcForm->error('Site.name') ?>
			<?php echo $this->BcForm->error('Site.alias') ?>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('Site.display_name', __d('baser', 'サイト名')) ?>&nbsp;<span
				class="required">*</span></th>
		<td>
			<?php echo $this->BcForm->input('Site.display_name', ['type' => 'input', 'size' => '60', 'counter' => true]) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div
				class="helptext"><?php echo __d('baser', 'サブサイト名を入力します。管理システムでの表示に利用されます。日本語の入力が可能ですのでわかりやすい名前をつけてください。') ?></div>
			<?php echo $this->BcForm->error('Site.display_name') ?>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('Site.title', __d('baser', 'サイトタイトル')) ?>&nbsp;<span
				class="required">*</span></th>
		<td>
			<?php echo $this->BcForm->input('Site.title', ['type' => 'input', 'size' => '60', 'counter' => true]) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div class="helptext"><?php echo __d('baser', 'サブサイトのタイトルを入力します。タイトルタグに利用されます。') ?></div>
			<?php echo $this->BcForm->error('Site.title') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('Site.keyword', __d('baser', 'サイト基本キーワード')) ?></th>
		<td class="col-input"><?php echo $this->BcForm->input('Site.keyword', ['type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width']) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpKeyword', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<?php echo $this->BcForm->error('Site.keyword') ?>
			<div id="helptextKeyword"
				 class="helptext"><?php echo __d('baser', 'テンプレートで利用する場合は、<br />&lt;?php $this->BcBaser->metaKeywords() ?&gt; で出力します。') ?></div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('Site.description', __d('baser', 'サイト基本説明文')) ?></th>
		<td class="col-input"><?php echo $this->BcForm->input('Site.description', ['type' => 'textarea', 'cols' => 36, 'rows' => 5, 'counter' => true]) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpDescription', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<?php echo $this->BcForm->error('Site.description') ?>
			<div id="helptextDescription"
				 class="helptext"><?php echo __d('baser', 'テンプレートで利用する場合は、<br />&lt;?php $this->BcBaser->metaDescription() ?&gt; で出力します。') ?></div>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('Site.main_site_id', __d('baser', 'メインサイト')) ?></th>
		<td>
			<?php echo $this->BcForm->input('Site.main_site_id', ['type' => 'select', 'options' => $mainSites]) ?>
			<?php echo $this->BcForm->input('Site.relate_main_site', ['type' => 'checkbox', 'label' => __d('baser', 'エイリアスを利用してメインサイトと自動連携する')]) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div class="helptext">
				<p><?php echo __d('baser', 'サブサイトの主として連携させたいサイトを選択します。') ?></p>
				<p>
					<?php echo __d('baser', '「エイリアスを利用してメインサイトと自動連携する」にチェックを入れておくと、メインサイトでコンテンツの追加や削除が発生した場合、<br>エイリアスを利用して自動的にサブサイトで同様の処理を実行します。') ?>
				</p>
			</div>
			<?php echo $this->BcForm->error('Site.main_site_id') ?>
		</td>
	</tr>
	<?php if ($useSiteDeviceSetting || $useSiteLangSetting): ?>
		<tr>
			<th><?php echo $this->BcForm->label('Site.device', __d('baser', 'デバイス・言語')) ?></th>
			<td>
				<?php if ($useSiteDeviceSetting): ?>
					<small><?php echo __d('baser', '[デバイス]') ?></small><?php echo $this->BcForm->input('Site.device', ['type' => 'select', 'options' => $devices]) ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div
						class="helptext"><?php echo __d('baser', 'サブサイトにデバイス属性を持たせ、サイトアクセス時、ユーザーエージェントを判定し適切なサイトを表示する機能を利用します。') ?></div>
				<?php else: ?>
					<?php echo $this->BcForm->input('Site.device', ['type' => 'hidden']) ?>
				<?php endif ?>
				<?php if ($useSiteLangSetting): ?>
					<small><?php echo __d('baser', '[言語]') ?></small><?php echo $this->BcForm->input('Site.lang', ['type' => 'select', 'options' => $langs]) ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div
						class="helptext"><?php echo __d('baser', 'サブサイトに言語属性を持たせ、サイトアクセス時、ブラウザの言語設定を判定し適切なサイトを表示する機能を利用します。') ?></div>
				<?php else: ?>
					<?php echo $this->BcForm->input('Site.lang', ['type' => 'hidden']) ?>
				<?php endif ?>
				<div id="SectionAccessType" style="display:none">
					<small><?php echo __d('baser', '[アクセス設定]') ?></small>
					<br>
					<span
						id="SpanSiteSameMainUrl"><?php echo $this->BcForm->input('Site.same_main_url', ['type' => 'checkbox', 'label' => __d('baser', 'メインサイトと同一URLでアクセス')]) ?>&nbsp;
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div
						class="helptext"><?php echo __d('baser', 'メインサイトと同一URLでアクセスし、デバイス設定や言語設定を判定し、適切なサイトを表示します。このオプションをオフにした場合は、エイリアスを利用した別URLを利用したアクセスとなります。') ?></div>
				</span>
					<br>
					<span
						id="SpanSiteAutoRedirect"><?php echo $this->BcForm->input('Site.auto_redirect', ['type' => 'checkbox', 'label' => __d('baser', 'メインサイトから自動的にリダイレクト')]) ?>&nbsp;
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<span
						class="helptext"><?php echo __d('baser', 'メインサイトと別URLでアクセスする際、デバイス設定や言語設定を判定し、適切なサイトへリダイレクトします。') ?></span>　
				</span>
					<span
						id="SpanSiteAutoLink"><?php echo $this->BcForm->input('Site.auto_link', ['type' => 'checkbox', 'label' => __d('baser', '全てのリンクをサブサイト用に変換する')]) ?>&nbsp;
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<span
						class="helptext"><?php echo __d('baser', 'メインサイトと別URLでアクセスし、エイリアスを利用して同一コンテンツを利用する場合、コンテンツ内の全てのリンクをサブサイト用に変換します。') ?></span>
				</span>
				</div>
				<?php echo $this->BcForm->error('Site.device') ?>
				<?php echo $this->BcForm->error('Site.lang') ?>
			</td>
		</tr>
	<?php endif ?>
	<tr>
		<th><?php echo $this->BcForm->label('Site.theme', __d('baser', 'テーマ')) ?></th>
		<td>
			<?php echo $this->BcForm->input('Site.theme', ['type' => 'select', 'options' => $themes]) ?>
			<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
			<div
				class="helptext"><?php echo __d('baser', 'サブサイトのテンプレートは、各テンプレートの配置フォルダ内にサイト名のサブフォルダを作成する事で別途配置する事ができますが、テーマフォルダ自体を別にしたい場合はここでテーマを指定します。') ?></div>
			<?php echo $this->BcForm->error('Site.theme') ?>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('Site.status', __d('baser', '公開状態')) ?></th>
		<td>
			<?php echo $this->BcForm->input('Site.status', ['type' => 'radio', 'options' => [0 => __d('baser', '公開しない'), 1 => __d('baser', '公開する')]]) ?>
			<?php echo $this->BcForm->error('Site.status') ?>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>
