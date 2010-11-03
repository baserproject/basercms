<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] サイト設定 フォーム
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

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
<?php echo $formEx->create('SiteConfig',array('action'=>'form')) ?> <?php echo $formEx->hidden('SiteConfig.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.name', 'WEBサイト名') ?></th>
		<td class="col-input"><?php echo $formEx->text('SiteConfig.name', array('size'=>35,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('SiteConfig.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>サイトの基本タイトルとして利用されます。</li>
					<li>テンプレートで利用する場合は、<br />
						&lt;?php $baser->title() ?&gt; で出力します。</li>
				</ul>
			</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.keyword', 'サイト基本キーワード') ?></th>
		<td class="col-input"><?php echo $formEx->text('SiteConfig.keyword', array('size'=>35,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpKeyword','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('SiteConfig.keyword') ?>
			<div id="helptextKeyword" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $baser->keywords() ?&gt; で出力します。</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.description', 'サイト基本説明文') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('SiteConfig.description', array('cols'=>36,'rows'=>5,'style'=>'width:80%')) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpDescription','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('SiteConfig.description') ?>
			<div id="helptextDescription" class="helptext">テンプレートで利用する場合は、<br />
				&lt;?php $baser->description() ?&gt; で出力します。</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.email', '管理者メールアドレス') ?></th>
		<td class="col-input"><?php echo $formEx->text('SiteConfig.email', array('size'=>35,'maxlength'=>255)) ?> <?php echo $formEx->error('SiteConfig.email') ?></td>
	</tr>
</table>
<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.address', 'GoogleMaps住所') ?></th>
		<td class="col-input"><?php echo $formEx->text('SiteConfig.address', array('size'=>35,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAddress','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('SiteConfig.address') ?>
			<div id="helptextAddress" class="helptext">GoogleMapを利用する場合は住所を入力して下さい。</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.googlemaps_key', 'GoogleMapsキー') ?></th>
		<td class="col-input"><?php echo $formEx->text('SiteConfig.googlemaps_key', array('size'=>35,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpGooglemapsKey','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('SiteConfig.googlemaps_key') ?>
			<div id="helptextGooglemapsKey" class="helptext"> GoogleMaps利用時のAPIキーを指定します。<br />
				GoogleMapを利用する場合には、Googleアカウントを取得した上で、<a href="http://code.google.com/intl/ja/apis/maps/signup.html" target="_blank">Google MAPS API に登録</a>しキーを取得します。<br />
				テンプレートで利用する場合は、 &lt;?php $baser->element('googlemaps') ?&gt; で出力します。 </div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.widget_area', '標準ウィジェットエリア') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('SiteConfig.widget_area', $formEx->getControlSource('WidgetArea.id'), null,null,false) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpWidgetArea','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextWidgetArea" class="helptext">
				公開ページ全般で利用するウィジェットエリアを指定します。<br />
				ウィジェットエリアは「<?php $baser->link('ウィジェットエリア管理',array('controller'=>'widget_areas','action'=>'index')) ?>」より追加できます。
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.theme', 'テーマ') ?></th>
		<td class="col-input"><?php echo $formEx->select('SiteConfig.theme', $themes,null,null,'なし') ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpTheme','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextTheme" class="helptext"> 公開ページのデザインテーマを指定します。<br />
				テーマを追加する場合には、<br />
				/app/webroot/themed/[テーマ名]/ としてテーマフォルダを作成し、
				そのフォルダの中にCakePHPのテンプレートファイルやcss、javascriptファイル等を配置します。<br />
				※ テーマ名には半角小文字のアルファベットを利用します。 </div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('SiteConfig.maintenance', '公開状態') ?></th>
		<td class="col-input"><?php echo $formEx->select('SiteConfig.maintenance', array(0=>'公開中',1=>'メンテナンス中'),null,null,false) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpMaintenance','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextMaintenance" class="helptext">
				公開状態を指定します。<br />
				メンテナンス中の場合は、管理画面にログインする事で公開ページを確認する事ができますが、
				制作・開発モードがデバッグモードの場合は、メンテナンス中にしていても公開ページが表示されてしまいますので注意が必要です。
				</div>
			</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.mode', '制作・開発モード') ?></th>
		<td class="col-input"><?php echo $formEx->select('SiteConfig.mode', $formEx->getControlSource('mode'),null,null,false) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpDebug','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextDebug" class="helptext"> 制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br />
				※ CakePHPのデバッグモードを指します。<br />
				※ インストールモードはBaserCMSを初期化する場合にしか利用しませんので普段は利用しないようにしてください。</div></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('SiteConfig.smart_url', 'スマートURL') ?></th>
		<td class="col-input">
			<span>利用可否：<strong>
			<?php if($rewriteInstalled === -1): ?>不明<?php elseif($rewriteInstalled): ?>可<?php else: ?>不可<?php endif ?></strong></span><br />
			<?php if(!$smartUrlChangeable) $disabled = array('disabled'=>'disabled') ?>
			<?php echo $formEx->select('SiteConfig.smart_url', array('0'=>'オフ', '1' => 'オン'), null ,$disabled, false) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpSmartUrl','class'=>'help','alt'=>'ヘルプ')) ?><br />
			<div id="helptextSmartUrl" class="helptext"></div>
			<?php if(!$writableInstall): ?><span class="error">≫ 変更するには、 <?php echo baseUrl() ?>app/config/install.php に書込権限を与えてください。</span><br /><?php endif ?>
			<?php if(!$writableHtaccess): ?><span class="error">≫ 変更するには、 <?php echo baseUrl() ?>.htaccess に書込権限を与えてください。</span><br /><?php endif ?>
			<?php if(!$writableHtaccess2): ?><span class="error">≫ 変更するには、 <?php echo baseUrl() ?>app/webroot/.htaccess に書込権限を与えてください。</span><?php endif ?>
		</td>
	</tr>
</table>
<div class="align-center"> <?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?> </div>
