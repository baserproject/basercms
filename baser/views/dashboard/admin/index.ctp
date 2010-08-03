<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ダッシュボード
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
	<p>ダッシュボードはログインした際に一番初めに来るページです。初期状態では、管理画面の利用履歴（最近の動き）とBaserCMS公式の更新情報が表示されます。</p>
		<p>画面上部のグローバルメニューは、初期状態では、BaserCMSの利用方法がイメージしやすいようにコーポレートサイト向けに最適化されていますが、
		<?php $baser->link('グローバルメニュー管理',array('controller'=>'global_menus','action'=>'index')) ?>よりカスタマイズする事ができます。（検索欄で「管理画面」を選択して検索してください）</p>
		<p>また、画面左のサイドメニューも、テンプレートを編集する事でカスタマイズする事ができます。</p>
	<div class="example-box">
		<div class="head">（例）ダッシュボードサイドメニューのテンプレートの場所</div>
		<p><?php $baser->link('上部メニューの「システム設定」',array('controller'=>'site_configs','action'=>'form')) ?> ≫
			<?php $baser->link('左メニューの「テーマ設定」',array('controller'=>'themes','action'=>'index')) ?> ≫
			<?php $baser->link('現在のテーマの「管理」ボタン',array('controller'=>'theme_files','action'=>'index',$siteConfig['theme'])) ?> ≫
			<?php $baser->link('左メニュー「エレメント一覧」',array('controller'=>'theme_files','action'=>'index',$siteConfig['theme'],'elements')) ?> ≫
			<?php $baser->link('admin/ の「開く」ボタン',array('controller'=>'theme_files','action'=>'index',$siteConfig['theme'],'elements/admin')) ?> ≫
			<?php $baser->link('submenus/ の「開く」ボタン',array('controller'=>'theme_files','action'=>'index',$siteConfig['theme'],'elements/admin/submenus')) ?> ≫
			<?php $baser->link('dashboard.ctp の「編集」ボタン',array('controller'=>'theme_files','action'=>'edit',$siteConfig['theme'],'elements/admin/submenus/dashboard.ctp')) ?>
		</p>
	</div>
</div>
<div class="float-left">
	<div id="ranking" class="box-01">
		<div class="box-head">
			<h3>最近の動き</h3>
		</div>
		<div class="box-body">
			<?php if($viewDblogs): ?>
			<ul>
				<?php foreach ($viewDblogs as $record): ?>
				<li><?php echo $time->format('Y.m.d',$record['Dblog']['created']) ?><br />
					<?php echo $record['Dblog']['name'] ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
		<div class="box-foot"> &nbsp; </div>
	</div>
</div>
<div class="float-right">
	<div id="ranking" class="box-01">
		<div class="box-head">
			<h3>BaserCMSニュース</h3>
		</div>
		<div class="box-body"> <?php echo $javascript->link('/feed/ajax/2') ?> </div>
		<div class="box-foot"> &nbsp; </div>
	</div>
</div>