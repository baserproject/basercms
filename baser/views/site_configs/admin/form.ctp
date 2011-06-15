<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] サイト設定 フォーム
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
$(function(){
	var theme = $("#SiteConfigTheme").val();
	<?php if($safeModeOn): ?>
	var safeModeOn = 1;
	<?php else: ?>
	var safeModeOn = 0;
	<?php endif ?>
	<?php if($formEx->value('SiteConfig.smart_url')): ?>
	var smartUrl = 1;
	<?php else: ?>
	var smartUrl = 0;
	<?php endif ?>
	var smartUrlAlert = 'スマートURLの設定を変更されていますが、ヘルプメッセージは読まれましたか？\nサーバー環境によっては、設定変更後、.htaccessファイルを手動で調整しないとアクセスできない場合もありますのでご注意ください。';
	var safemodeAlert = '機能制限のセーフモードで動作しています。テーマの切り替えを行う場合、あらかじめ切り替え対象のテーマ内に、データベースに登録されているページカテゴリ用のフォルダを作成しておき、書込権限を与えておく必要があります。\n'+
						'ページカテゴリ用のフォルダが存在しない状態でテーマの切り替えを実行すると、対象ページカテゴリ内のWebページは正常に表示できなくなりますのでご注意ください。';
	$("#btnSubmit").click(function(){
		var result = true;
		if(smartUrl != $("#SiteConfigSmartUrl").val()) {
			if(!confirm(smartUrlAlert)){
				result = false;
			}
		}
		if(safeModeOn && (theme != $("#SiteConfigTheme").val())) {
			if(!confirm(safemodeAlert)) {
				result = false;
			}
		}
		return result;
	});
});
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>WEBサイトの基本設定を行います。<br />
		各項目のヘルプメッセージをご確認ください。</p>
	<ul>
		<li>サイドメニューの「グローバルメニュー設定」から、公開ページ、管理画面のグローバルメニューの設定ができます。</li>
		<li>サイドメニューの「テーマ設定」から、テーマファイルの閲覧、編集、削除等ができます。</li>
		<li>サイドメニューの「プラグイン設定」から、各種プラグインの管理を行う事ができます。</li>
		<li>サイドメニューの「データバックアップ」から、データベースに格納されたデータのバックアップを行う事ができます。</li>
		<li>サイドメニューの「サーバーキャッシュ削除」で、サーバー上のキャッシュファイルを全て削除する事ができます。
			また、画面下部オプションの「制作・開発モード」をデバッグモードに切り替えると、サーバーキャッシュを削除した上で、新たに生成しないようにする事ができます。</li>
	</ul>
</div>

<h3>基本項目</h3>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('SiteConfig',array('action'=>'form')) ?>
<?php echo $formEx->hidden('SiteConfig.id') ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.formal_name', 'WEBサイト名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('SiteConfig.formal_name', array('type' => 'text', 'size' => 55,'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpFormalName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('SiteConfig.formal_name') ?>
			<div id="helptextFormalName" class="helptext">
				<ul>
					<li>正式なWEBサイト名を指定します。</li>
					<li>メールの送信元等で利用します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.name', 'WEBサイトタイトル') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('SiteConfig.name', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('SiteConfig.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>Webサイトの基本タイトルとして利用されます。（タイトルタグに影響します）</li>
					<li>テンプレートで利用する場合は、<br />
						&lt;?php $baser->title() ?&gt; で出力します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.keyword', 'サイト基本キーワード') ?></th>
		<td class="col-input"><?php echo $formEx->input('SiteConfig.keyword', array('type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpKeyword', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('SiteConfig.keyword') ?>
			<div id="helptextKeyword" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $baser->keywords() ?&gt; で出力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.description', 'サイト基本説明文') ?></th>
		<td class="col-input"><?php echo $formEx->input('SiteConfig.description', array('type' => 'textarea', 'cols' => 36, 'rows' => 5, 'style' => 'width:80%', 'counter' => true)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpDescription', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('SiteConfig.description') ?>
			<div id="helptextDescription" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $baser->description() ?&gt; で出力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.email', '管理者メールアドレス') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('SiteConfig.email', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $formEx->error('SiteConfig.email') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.admin_list_num', '管理システムの<br />初期一覧件数') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('SiteConfig.admin_list_num', array('type' => 'select', 'options' => array(
				10	=> '10件',
				20	=> '20件',
				50	=> '50件',
				100 => '100件'
			))) ?>
			<?php echo $formEx->error('SiteConfig.admin_list_num') ?>
		</td>
	</tr>
</table>

<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>

<div id ="formOptionBody" class="slide-body">
	<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
		<tr>
			<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.site_url', 'WebサイトURL') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.site_url', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?><br />
				<?php echo $formEx->input('SiteConfig.ssl_url', array('type' => 'text', 'size' => 35, 'maxlength' => 255, 'after' => '<small>[SSL]</small>')) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSiteUrl', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('SiteConfig.site_url') ?>
				<?php echo $formEx->error('SiteConfig.ssl_url') ?>
				<div id="helptextSiteUrl" class="helptext">BaserCMSを設置しているURLを指定します。管理画面等でSSL通信を利用する場合は、SSL通信で利用するURLも指定します。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.admin_ssl_on', '管理画面SSL設定') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.admin_ssl_on', array('type' => 'radio', 'options' => $textEx->booleanDoList('SSL通信を利用'), 'separator' => '　', 'legend'=>false)) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdminSslOn', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('SiteConfig.admin_ssl_on') ?>
				<div id="helptextAdminSslOn" class="helptext">管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。<br />
					また、SSL用のWebサイトURLの指定が必要です。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.address', 'GoogleMaps住所') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.address', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id'=>'helpAddress', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('SiteConfig.address') ?>
				<div id="helptextAddress" class="helptext">GoogleMapを利用する場合は住所を入力してください。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.google_analytics_id', 'Google Analytics<br />ウェブプロパティID') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.google_analytics_id', array('type' => 'text', 'size' => 35, 'maxlength' => 16)) ?>
				<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpGoogleAnalyticsId', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('SiteConfig.google_analytics_id') ?>
				<div id="helptextGoogleAnalyticsId" class="helptext">
					<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> 利用時の「UA」から始まる「ウェブプロパティID」を入力します。<br />
					<a href="http://www.google.com/intl/ja/analytics/" target="_blank">Google Analytics</a> を利用するにはあらかじめ Google アカウントの取得が必要です。<br />
					テンプレートで利用する場合は、 <pre>&lt;?php $baser->element('google_analytics') ?&gt;</pre> で出力します。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.widget_area', '標準ウィジェットエリア') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.widget_area', array('type' => 'select', 'options' => $formEx->getControlSource('WidgetArea.id'), 'empty' => 'なし')) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpWidgetArea', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextWidgetArea" class="helptext">
					公開ページ全般で利用するウィジェットエリアを指定します。<br />
					ウィジェットエリアは「<?php $baser->link('ウィジェットエリア管理',array('controller'=>'widget_areas','action'=>'index')) ?>」より追加できます。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.theme', 'テーマ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.theme', array('type' => 'select', 'options' => $themes, 'empty' => 'なし')) ?>
				<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpTheme', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextTheme" class="helptext"> 公開ページのデザインテーマを指定します。<br />
					テーマを追加する場合には、<br />
					/app/webroot/themed/[テーマ名]/ としてテーマフォルダを作成し、
					そのフォルダの中にCakePHPのテンプレートファイルやcss、javascriptファイル等を配置します。<br />
					※ テーマ名には半角小文字のアルファベットを利用します。 </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.maintenance', '公開状態') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.maintenance', array('type' => 'select' , 'options' => array(0 => '公開中', 1 => 'メンテナンス中'))) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpMaintenance', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextMaintenance" class="helptext">
					公開状態を指定します。<br />
					メンテナンス中の場合は、管理画面にログインする事で公開ページを確認する事ができますが、
					制作・開発モードがデバッグモードの場合は、メンテナンス中にしていても公開ページが表示されてしまいますので注意が必要です。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.mode', '制作・開発モード') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.mode', array('type' => 'select' , 'options' => $formEx->getControlSource('mode'))) ?>
				<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpDebug', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextDebug" class="helptext">制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br />
					※ CakePHPのデバッグモードを指します。<br />
					※ インストールモードはBaserCMSを初期化する場合にしか利用しませんので普段は利用しないようにしてください。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head">
				<?php echo $formEx->label('SiteConfig.smart_url', 'スマートURL') ?><br />
				<small class="error">設定を変更する場合は<br />「？」マークのヘルプを<br />必ずお読みください</small>
			</th>
			<td class="col-input">
				<span>Rewriteモジュール利用可否：<strong>
				<?php if($rewriteInstalled === -1): ?>不明<?php elseif($rewriteInstalled): ?>可<?php else: ?>不可<?php endif ?></strong></span><br />
				<?php $disabled = array() ?>
				<?php if(!$smartUrlChangeable) $disabled = array('disabled'=>'disabled') ?>
				<?php echo $formEx->input('SiteConfig.smart_url', array('type' => 'select', 'options' => array('0'=>'オフ', '1' => 'オン'))) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSmartUrl', 'class' => 'help', 'alt' => 'ヘルプ')) ?><br />
				<div id="helptextSmartUrl" class="helptext">
					<p>短くスマートなURLを実現するにはApache Rewriteモジュールと.htaccessの利用許可が必要です。<br />
					・スマートURL無効例：<br />　　http://localhost/index.php/contact/form<br />
					・スマートURL有効例：<br />　　http://localhost/contact/form<br />
					</p><br />
					<p>スマートURLの設定はサーバー環境に深く依存します。<br />
						「オン」に変更した場合、サーバーエラーとなり画面にアクセスできなくなる可能性もありますので注意が必要です。<br />
						スマートURLをオンに切り替えた場合、/ フォルダ、/app/webroot/ フォルダ内の「.htaccess」ファイルに、
						RewriteBase設定を自動的に書き込みますが、うまく動作しない場合、この設定値を環境に合わせて調整する必要があります。<br />
						詳細については、各.htaccessファイルのコメントを確認してください。</p>
				</div>
				<?php if(!$writableInstall): ?><span class="error">≫ 変更するには、 <?php echo $baseUrl ?>app/config/install.php に書込権限を与えてください。</span><br /><?php endif ?>
				<?php if(!$writableHtaccess): ?><span class="error">≫ 変更するには、 <?php echo $baseUrl ?>.htaccess に書込権限を与えてください。</span><br /><?php endif ?>
				<?php if(!$writableHtaccess2): ?><span class="error">≫ 変更するには、 <?php echo $baseUrl ?>app/webroot/.htaccess に書込権限を与えてください。</span><?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.mobile', 'モバイル') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.mobile', array('type' => 'radio', 'options' => $textEx->booleanDoList('対応'))) ?>
			</td>
		</tr>
<?php if($baser->siteConfig['category_permission']): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('SiteConfig.mobile', 'ルート管理グループ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.root_owner_id', array('type' => 'select', 'options' => $userGroups, 'empty' => '指定しない')) ?>
			</td>
		</tr>
<?php endif ?>
	</table>
	
	<h3>メール設定関連</h3>
	
	<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
		<tr>
			<th><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.mail_encode', 'メール送信文字コード') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.mail_encode', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpEncode', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextEncode" class="helptext">送信メールの文字コード</div>
				<?php echo $formEx->error('SiteConfig.mail_encode') ?>
			</td>
		</tr>
		<tr>
			<th><?php echo $formEx->label('SiteConfig.smtp_host', 'SMTPホスト') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.smtp_host', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $formEx->error('SiteConfig.smtp_host') ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSmtpHost', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpHost" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $formEx->label('SiteConfig.smtp_user', 'SMTPユーザー') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.smtp_user', array('type' => 'text', 'size'=>35,'maxlength'=>255)) ?>
				<?php echo $formEx->error('SiteConfig.smtp_user') ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSmtpUsername', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpUsername" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $formEx->label('SiteConfig.smtp_password', 'SMTPパスワード') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('SiteConfig.smtp_password', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
				<?php echo $formEx->error('SiteConfig.smtp_password') ?>
				<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSmtpPassword', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSmtpPassword" class="helptext">メールの送信にSMTPサーバーを利用する場合指定します。</div>
			</td>
		</tr>
	</table>
</div>

<div class="align-center">
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button', 'id' => 'btnSubmit')) ?>
</div>

<?php echo $formEx->end() ?>