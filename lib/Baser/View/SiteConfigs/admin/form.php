<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [管理画面] サイト設定 フォーム
 * @var BcAppView $this
 * @var array $disableSettingInstallSetting install.php について、 disabled を設定するかどうか
 * @var array $themes テーマ一覧
 * @var bool $safeModeOn セーフモードかどうか
 */
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', '管理システムをSSLに切り替える場合には、SSL用のURLを登録してください。'),
	'alertMessage2' => __d('baser', "機能制限のセーフモードで動作しています。テーマの切り替えを行う場合、あらかじめ切り替え対象のテーマ内に、データベースに登録されているページカテゴリ用のフォルダを作成しておき、書込権限を与えておく必要があります。\nページカテゴリ用のフォルダが存在しない状態でテーマの切り替えを実行すると、対象ページカテゴリ内のWebページは正常に表示できなくなりますのでご注意ください。"),
	'alertMessage3' => __d('baser', 'テストメールを送信に失敗しました。'),
	'confirmMessage1' => __d('baser', '管理システムをSSLに切り替えようとしています。よろしいですか？<br><br>サーバがSSLに対応していない場合、管理システムを表示する事ができなくなってしまいますのでご注意ください。<br><br>もし、表示する事ができなくなってしまった場合は、 /app/Config/install.php の、 BcEnv.sslUrl の値を調整するか、BcApp.adminSsl の値を false に書き換えて復旧してください。'),
	'confirmMessage2' => __d('baser', 'テストメールを送信します。いいですか？'),
	'infoMessage1' => __d('baser', 'テストメールを送信しました。'),
	'confirmTitle1' => __d('baser', '管理システムSSL設定確認')
], ['escape' => false]);
$this->BcBaser->js('admin/site_configs/form', false, ['id' => 'AdminSiteConfigsFormScript',
	'data-safeModeOn' => (string)$safeModeOn,
	'data-isAdminSsl' => (string)$this->request->data['SiteConfig']['admin_ssl']
]);
?>


<h2><?php echo __d('baser', '基本項目') ?></h2>


<?php echo $this->BcForm->create('SiteConfig', ['url' => ['action' => 'form']]) ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcForm->hidden('SiteConfig.id') ?>


<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table section">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.formal_name', __d('baser', 'Webサイト名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.formal_name', ['type' => 'text', 'size' => 55, 'maxlength' => 255, 'autofocus' => true, 'class' => 'full-width']) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpFormalName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.formal_name') ?>
				<div id="helptextFormalName" class="helptext">
					<ul>
						<li><?php echo __d('baser', '正式なWebサイト名を指定します。') ?></li>
						<li><?php echo __d('baser', 'メールの送信元等で利用します。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.name', __d('baser', 'Webサイトタイトル')) ?>&nbsp;<span
					class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.name', ['type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width']) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'Webサイトの基本タイトルとして利用されます。（タイトルタグに影響します）') ?></li>
						<li><?php echo __d('baser', 'テンプレートで利用する場合は、<br />&lt;?php $this->BcBaser->title() ?&gt; で出力します。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.keyword', __d('baser', 'サイト基本キーワード')) ?></th>
			<td class="col-input"><?php echo $this->BcForm->input('SiteConfig.keyword', ['type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width']) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpKeyword', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.keyword') ?>
				<div id="helptextKeyword"
					 class="helptext"><?php echo __d('baser', 'テンプレートで利用する場合は、<br />&lt;?php $this->BcBaser->metaKeywords() ?&gt; で出力します。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.description', __d('baser', 'サイト基本説明文')) ?></th>
			<td class="col-input"><?php echo $this->BcForm->input('SiteConfig.description', ['type' => 'textarea', 'cols' => 36, 'rows' => 5, 'counter' => true]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpDescription', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.description') ?>
				<div id="helptextDescription"
					 class="helptext"><?php echo __d('baser', 'テンプレートで利用する場合は、<br />&lt;?php $this->BcBaser->metaDescription() ?&gt; で出力します') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.email', __d('baser', '管理者メールアドレス')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.email', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->error('SiteConfig.email') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_list_num', __d('baser', '管理システムの<br />初期一覧件数')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('SiteConfig.admin_list_num', ['type' => 'select', 'options' => [
					10 => __d('baser', '10件'),
					20 => __d('baser', '20件'),
					50 => __d('baser', '50件'),
					100 => __d('baser', '100件')
				]])
				?>
				<?php echo $this->BcForm->error('SiteConfig.admin_list_num') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_list_num', __d('baser', '管理システムの<br>テーマ')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.admin_theme', ['type' => 'select', 'options' => $themes]) ?>
				<?php echo $this->BcForm->error('SiteConfig.admin_theme') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption"><?php echo __d('baser', 'オプション') ?></a></h2>

<div id="formOptionBody" class="slide-body section">
	<table class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.login_credit', __d('baser', 'ログインページのクレジット表示')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.login_credit', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(__d('baser', '利用'))]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div class="helptext"><?php echo __d('baser', 'ログインページに表示されているクレジット表示を利用するかどうか設定します。') ?></div>
				<?php echo $this->BcForm->error('SiteConfig.login_credit') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_side_banner', __d('baser', '管理システムサイドバーの<br />バナー表示')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.admin_side_banner', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(__d('baser', '利用'))]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div class="helptext"><?php echo __d('baser', '管理システムのサイド部分にバナーを表示するかどうか設定します。') ?></div>
				<?php echo $this->BcForm->error('SiteConfig.admin_side_banner') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.site_url', __d('baser', 'WebサイトURL')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.site_url', array_merge(['type' => 'text', 'size' => 35, 'maxlength' => 255], $disableSettingInstallSetting)) ?>
				<br/>
				<?php echo $this->BcForm->input('SiteConfig.ssl_url', array_merge(['type' => 'text', 'size' => 35, 'maxlength' => 255, 'after' => '<small>[SSL]</small>'], $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpSiteUrl', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php if ($disableSettingInstallSetting): ?>
					<?php echo $this->BcForm->input('SiteConfig.site_url', ['type' => 'hidden']) ?>
				<?php endif ?>
				<?php echo $this->BcForm->error('SiteConfig.site_url') ?>
				<?php echo $this->BcForm->error('SiteConfig.ssl_url') ?>
				<div id="helptextSiteUrl"
					 class="helptext"><?php echo __d('baser', 'baserCMSを設置しているURLを指定します。管理画面等でSSL通信を利用する場合は、SSL通信で利用するURLも指定します。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.admin_ssl', __d('baser', '管理画面SSL設定')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.admin_ssl', array_merge(['type' => 'radio', 'options' => $this->BcText->booleanDoList(__d('baser', 'SSL通信を利用')), 'separator' => '　', 'legend' => false], $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpAdminSsl', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.admin_ssl') ?>
				<div id="helptextAdminSslOn"
					 class="helptext"><?php echo __d('baser', '管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。<br />また、SSL用のWebサイトURLの指定が必要です。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.address', __d('baser', 'GoogleMaps住所')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.address', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'placeholder' => __d('baser', '住所')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpAddress', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextAddress"
					 class="helptext"><?php echo __d('baser', 'GoogleMapを利用する場合は地図を表示させたい住所を入力してください。郵便番号からでも大丈夫です。<br><br>入力例1) 福岡市中央区大名2-11-25<br>入力例2) 〒819-0041 福岡県福岡市中央区大名2-11-25<br><br>建物名を含めるとうまく表示されない場合があります。<br>その時は建物名を省略して試してください。<br>APIキーを入力しないと地図が表示されない場合があります。<a href="https://developers.google.com/maps/web/" target="_blank">「ウェブ向け Google Maps API」</a>') ?></div>
				<br/>
				<?php echo $this->BcForm->input('SiteConfig.google_maps_api_key', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'placeholder' => __d('baser', 'APIキー')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.address') ?>
				<?php echo $this->BcForm->error('SiteConfig.google_maps_api_key') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.google_analytics_id', __d('baser', 'Google Analytics<br />トラッキングID')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.google_analytics_id', ['type' => 'text', 'size' => 35, 'maxlength' => 16]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpGoogleAnalyticsId', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextGoogleAnalyticsId" class="helptext">
					<?php echo __d('baser', 'Googleの無料のアクセス解析サービス <a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> を利用される方は、取得したトラッキングID (UA-000000-01 のような文字列）を入力してください。') ?>
					<br/>
					※<?php echo __d('baser', '事前に<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> で登録作業が必要です。') ?>
					<br/>
					<?php echo __d('baser', 'テンプレートで利用する場合は、 <pre>&lt;?php $this->BcBaser->googleAnalytics() ?&gt;</pre> で出力します。') ?>
				</div>
				<?php echo $this->BcForm->error('SiteConfig.google_analytics_id') ?><br/>
				<?php echo sprintf(__d('baser', 'ユニバーサルアナリティクスを%s'), $this->BcForm->input('SiteConfig.use_universal_analytics', ['type' => 'radio', 'options' => ['0' => __d('baser', '利用していない'), '1' => __d('baser', '利用している')]])) ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.widget_area', __d('baser', '標準ウィジェットエリア')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.widget_area', ['type' => 'select', 'options' => $this->BcForm->getControlSource('WidgetArea.id'), 'empty' => __d('baser', 'なし')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextWidgetArea" class="helptext">
					<?php echo __d('baser', '公開ページ全般で利用するウィジェットエリアを指定します。') ?><br/>
					<?php echo sprintf(__d('baser', 'ウィジェットエリアは「%s」より追加できます。'), $this->BcBaser->getLink(__d('baser', 'ウィジェットエリア管理'), ['controller' => 'widget_areas', 'action' => 'index'])) ?>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.main_site_display_name', __d('baser', 'メインサイト表示名称')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.main_site_display_name', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div class="helptext"><?php echo __d('baser', 'サブサイトを利用する際に、メインサイトを特定する識別名称を設定します。') ?></div>
				<?php echo $this->BcForm->error('SiteConfig.main_site_display_name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.use_site_device_setting', __d('baser', 'デバイス・言語設定')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.use_site_device_setting', ['type' => 'checkbox', 'label' => __d('baser', 'サブサイトでデバイス設定を利用する')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div
					class="helptext"><?php echo __d('baser', 'サブサイトにデバイス属性を持たせ、サイトアクセス時、ユーザーエージェントを判定し適切なサイトを表示する機能を利用します。') ?></div>
				<?php echo $this->BcForm->input('SiteConfig.use_site_lang_setting', ['type' => 'checkbox', 'label' => __d('baser', 'サブサイトで言語設定を利用する')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div
					class="helptext"><?php echo __d('baser', 'サブサイトに言語属性を持たせ、サイトアクセス時、ブラウザの言語設定を判定し適切なサイトを表示する機能を利用します。') ?></div>
				<?php echo $this->BcForm->error('SiteConfig.use_site_device_setting') ?>
				<?php echo $this->BcForm->error('SiteConfig.use_site_lang_setting') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.maintenance', __d('baser', '公開状態')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.maintenance', ['type' => 'select', 'options' => [0 => __d('baser', '公開中'), 1 => __d('baser', 'メンテナンス中')]]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpMaintenance', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextMaintenance" class="helptext">
					<?php echo __d('baser', '公開状態を指定します。<br />メンテナンス中の場合に、公開ページを確認するには、管理画面にログインする必要があります。<br>ただし、制作・開発モードがデバッグモードに設定されている場合は、メンテナンス中にしていても公開ページが表示されてしまいますので注意が必要です。') ?>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.mode', __d('baser', '制作・開発モード')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mode', array_merge(['type' => 'select', 'options' => $this->BcForm->getControlSource('mode')], $disableSettingInstallSetting)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpDebug', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextDebug"
					 class="helptext"><?php echo __d('baser', '制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br />※ CakePHPのデバッグモードを指します。<br />※ インストールモードはbaserCMSを初期化する場合にしか利用しませんので普段は利用しないようにしてください。') ?></div>
			</td>
		</tr>
	</table>

	<h2><?php echo __d('baser', 'エディタ設定関連') ?></h2>

	<table class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_enter_br', __d('baser', 'エディタタイプ')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.editor', ['type' => 'radio', 'options' => Configure::read('BcApp.editors')]) ?>
			</td>
		</tr>
		<tr class="ckeditor-option">
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_enter_br', __d('baser', '改行モード')) ?></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('SiteConfig.editor_enter_br', ['type' => 'radio', 'options' => [
					'0' => __d('baser', '改行時に段落を挿入する'),
					'1' => __d('baser', '改行時にBRタグを挿入する')
				]])
				?>
			</td>
		</tr>
		<tr class="ckeditor-option">
			<th class="col-head"><?php echo $this->BcForm->label('SiteConfig.editor_styles', __d('baser', 'エディタスタイルセット')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.editor_styles', ['type' => 'textarea', 'cols' => 36, 'rows' => 10]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('SiteConfig.editor_styles') ?>
				<div id="helptextFormalName" class="helptext">
					<p><?php echo __d('baser', '固定ページなどで利用するエディタのスタイルセットをCSS形式で記述する事ができます。') ?></p>
					<pre># <?php echo __d('baser', 'タイトル') ?>
						<?php echo __d('baser', 'タグ') ?> {
						<?php echo __d('baser', 'プロパティ名：プロパティ値') ?>
}

 《<?php echo __d('baser', '記述例') ?>》
 # <?php echo __d('baser', '見出し') ?>
 h2 {
	font-size:20px;
	color:#333;
 }
					</pre>
					<p><?php echo __d('baser', 'タグにプロパティを設定しない場合は次のように記述します。') ?></p>
					<pre>
# <?php echo __d('baser', '見出し') ?>
h2 {}
					</pre>
				</div>
			</td>
		</tr>
	</table>

	<h2><?php echo __d('baser', 'メール設定関連') ?></h2>

	<table class="form-table">
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.mail_encode', __d('baser', 'メール送信文字コード')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mail_encode', ['type' => 'select', 'options' => Configure::read('BcEncode.mail')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpEncode', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextEncode"
					 class="helptext"><?php echo __d('baser', '送信メールの文字コードを選択します。<br />受信したメールが文字化けする場合に変更します。') ?></div>
				<?php echo $this->BcForm->error('SiteConfig.mail_encode') ?>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.smtp_host', __d('baser', 'SMTP設定')) ?></th>
			<td class="col-input">
				<div style="margin-bottom: 0.5em;">
					<?php echo $this->BcForm->label('SiteConfig.smtp_host', __d('baser', 'ホスト')) ?>
					<?php echo $this->BcForm->input('SiteConfig.smtp_host', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off']) ?>
					<?php echo $this->BcForm->error('SiteConfig.smtp_host') ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpSmtpHost', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div id="helptextSmtpHost"
						 class="helptext"><?php echo __d('baser', 'メールの送信にSMTPサーバーを利用する場合指定します。') ?></div>
				</div>
				<div style="margin-bottom: 0.5em;">
					<?php echo $this->BcForm->label('SiteConfig.smtp_port', __d('baser', 'ポート')) ?>
					<?php echo $this->BcForm->input('SiteConfig.smtp_port', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off']) ?>
					<?php echo $this->BcForm->error('SiteConfig.smtp_port') ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div
						class="helptext"><?php echo __d('baser', 'メールの送信にSMTPサーバーを利用する場合指定します。入力を省略した場合、25番ポートを利用します。') ?></div>
				</div>
				<div style="margin-bottom: 0.5em;">
					<?php echo $this->BcForm->label('SiteConfig.smtp_user', __d('baser', 'ユーザー')) ?>
					<?php echo $this->BcForm->input('SiteConfig.smtp_user', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off']) ?>
					<?php echo $this->BcForm->error('SiteConfig.smtp_user') ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpSmtpUsername', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div id="helptextSmtpUsername"
						 class="helptext"><?php echo __d('baser', 'メールの送信にSMTPサーバーを利用する場合指定します。') ?></div>
				</div>
				<div style="margin-bottom: 0.5em;">
					<!-- ↓↓↓自動入力を防止する為のダミーフィールド↓↓↓ -->
					<input type="password" name="dummypass" style="display: none;">
					<?php echo $this->BcForm->label('SiteConfig.smtp_password', __d('baser', 'パスワード')) ?>
					<?php echo $this->BcForm->input('SiteConfig.smtp_password', ['type' => 'password', 'size' => 35, 'maxlength' => 255, 'autocomplete' => 'off']) ?>
					<?php echo $this->BcForm->error('SiteConfig.smtp_password') ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpSmtpPassword', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div id="helptextSmtpPassword"
						 class="helptext"><?php echo __d('baser', 'メールの送信にSMTPサーバーを利用する場合指定します。') ?></div>
				</div>
				<div style="margin-bottom: 1.5em;">
					<?php echo $this->BcForm->label('SiteConfig.smtp_tls', __d('baser', 'TLS暗号化')) ?>
					<?php echo $this->BcForm->input('SiteConfig.smtp_tls', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(__d('baser', 'TLS暗号化を利用'))]) ?>
					<?php echo $this->BcForm->error('SiteConfig.smtp_tls') ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpSmtpTls', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div id="helptextSmtpTls"
						 class="helptext"><?php echo __d('baser', 'SMTPサーバーがTLS暗号化を利用する場合指定します。') ?></div>
				</div>
				<p>
					<?php echo $this->BcForm->button(__d('baser', 'メール送信テスト'), ['type' => 'button', 'class' => 'button-small', 'id' => 'BtnCheckSendmail']) ?>
					　<span id=ResultCheckSendmail></span>
					<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['id' => 'AjaxLoaderCheckSendmail', 'style' => 'display:none']) ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('SiteConfig.mail_additional_parameters', __d('baser', 'additional_parameters（オプション）')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SiteConfig.mail_additional_parameters', ['type' => 'input', 'size' => 35, 'maxlength' => 255, 'placeholder' => '-f webmaster@mail.example.com']) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpEncode', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div class="helptext"><?php echo __d('baser', '標準機能によるメール送信時にオプションを追加します。') ?></div>
				<?php echo $this->BcForm->error('SiteConfig.mail_additional_parameters') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
</div>

<?php echo $this->BcForm->end() ?>
