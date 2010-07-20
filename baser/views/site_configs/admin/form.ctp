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
<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
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
<?php echo $form->create('SiteConfig',array('action'=>'form')) ?>
<?php echo $form->hidden('SiteConfig.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<tr>
<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('SiteConfig.name', 'WEBサイト名') ?></th>
	<td class="col-input">
        <?php echo $form->text('SiteConfig.name', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.name') ?>
        <div id="helptextName" class="helptext">
            <ul>
                <li>サイトの基本タイトルとして利用されます。</li>
                <li>テンプレートで利用する場合は、<br />&lt;?php $baser->title() ?&gt; で出力します。</li>
            </ul>
        </div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.keyword', 'サイト基本キーワード') ?></th>
	<td class="col-input">
        <?php echo $form->text('SiteConfig.keyword', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpKeyword','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.keyword') ?>
        <div id="helptextKeyword" class="helptext">テンプレートで利用する場合は、<br />&lt;?php $baser->keywords() ?&gt; で出力します。</div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.description', 'サイト基本説明文') ?></th>
	<td class="col-input">
        <?php echo $form->textarea('SiteConfig.description', array('cols'=>36,'rows'=>5,'style'=>'width:80%')) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpDescription','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.description') ?>
        <div id="helptextDescription" class="helptext">テンプレートで利用する場合は、<br />&lt;?php $baser->description() ?&gt; で出力します。</div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('SiteConfig.email', '管理者メールアドレス') ?></th>
	<td class="col-input">
    	<?php echo $form->text('SiteConfig.email', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $form->error('SiteConfig.email') ?>
    </td>
</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.address', 'GoogleMaps住所') ?></th>
	<td class="col-input">
        <?php echo $form->text('SiteConfig.address', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAddress','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.address') ?>
        <div id="helptextAddress" class="helptext">GoogleMapを利用する場合は住所を入力して下さい。</div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.googlemaps_key', 'GoogleMapsキー') ?></th>
	<td class="col-input">
        <?php echo $form->text('SiteConfig.googlemaps_key', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpGooglemapsKey','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.googlemaps_key') ?>
        <div id="helptextGooglemapsKey" class="helptext">
            GoogleMaps利用時のAPIキーを指定します。<br />
            GoogleMapを利用する場合には、Googleアカウントを取得した上で、<a href="http://code.google.com/intl/ja/apis/maps/signup.html" target="_blank">Google MAPS API に登録</a>しキーを取得します。<br />
            テンプレートで利用する場合は、 &lt;?php $baser->element('googlemaps') ?&gt; で出力します。
        </div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.twitter_account', 'Twitterユーザー名') ?></th>
	<td class="col-input">
        <?php echo $form->text('SiteConfig.twitter_username', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpTwitterUsername','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.twitter_username') ?>
        <div id="helptextTwitterUsername" class="helptext">
            Twitterログ読み込み機能を使う場合のTwitterユーザー名を指定します。<br />
            <a href="https://twitter.com/signup">≫ Twitterにサインアップ</a><br />
            テンプレートで利用する場合は、 &lt;?php $baser->element('tweet') ?&gt; で出力します。
        </div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.twitter_account', 'Twitterログ出力件数') ?></th>
	<td class="col-input">
        <?php echo $form->text('SiteConfig.twitter_count', array('size'=>35,'maxlength'=>255)) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpTwitterCount','class'=>'help','alt'=>'ヘルプ')) ?>
        <?php echo $form->error('SiteConfig.twitter_count') ?>
        <div id="helptextTwitterCount" class="helptext">
            Twitterログ読み込み機能を使う場合のログの出力件数を指定します。<br />
        </div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><?php echo $form->label('SiteConfig.theme', 'テーマ') ?></th>
	<td class="col-input">
    	<?php echo $form->select('SiteConfig.theme', $themes,null,null,'なし') ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpTheme','class'=>'help','alt'=>'ヘルプ')) ?>
        <div id="helptextTheme" class="helptext">
            公開ページのデザインテーマを指定します。<br />
            テーマを追加する場合には、<br />
            /app/webroot/themed/[テーマ名]/ としてテーマフォルダを作成し、
            そのフォルダの中にCakePHPのテンプレートファイルやcss、javascriptファイル等を配置します。<br />
            ※ テーマ名にはアルファベットを利用します。
        </div>
        &nbsp;
    </td>
</tr>
<tr>
<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('SiteConfig.mode', '制作・開発モード') ?></th>
	<td class="col-input">
    	<?php echo $form->select('SiteConfig.mode', $formEx->getControlSource('mode'),null,null,false) ?>
        <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpDebug','class'=>'help','alt'=>'ヘルプ')) ?>
        <div id="helptextDebug" class="helptext">
            制作・開発時のモードを指定します。通常は、ノーマルモードを指定しておきます。<br />
            ※ CakePHPのデバッグモードを指します。<br />
            ※ インストールモードはBaserCMSを初期化する場合にしか利用しません。
        </div>
    </td>
</tr>
</table>
<div class="align-center">
<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
</div>