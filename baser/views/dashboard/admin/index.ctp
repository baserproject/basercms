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
	&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ダッシュボードはログインした際に一番初めに来るページです。初期状態では、管理画面の利用履歴（最近の動き）とBaserCMS公式の更新情報が表示されます。<br />
		画面上部のグローバルメニューは、初期状態では、BaserCMSの利用方法がイメージしやすいようにコーポレートサイト向けに最適化されていますが、
		グローバルメニュー管理よりカスタマイズする事ができます。<br />
		また、画面左のサイドメニューも、テンプレートを編集する事でカスタマイズする事ができます。</p>
	<div class="example-box">
		<div class="head">（例）テーマ：Demo を利用している場合のダッシュボードサイドメニューのテンプレートの場所</div>
		<p>app/webroot/themed/demo/elements/admin/submenus/dashboard.ctp</p>
		<p><small>※ 各テンプレートはそのうち管理画面で編集できるように改善される予定です。</small></p>
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