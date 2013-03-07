<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ウィジェットエリア一覧　ヘルプ
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<p>ウィジェットとは簡単にWEBページの指定した場所に部品の追加・削除ができる仕組みです。<br />
	その部品の一つ一つをウィジェットと呼び、ウィジェットが集まった一つのグループをウィジェットエリアと呼びます。</p>
<p>全体で利用するウィジェットエリアは、「<?php $bcBaser->link("サイト基本設定",array('controller'=>'site_configs','action'=>'form')) ?>」で設定できます。また、標準プラグインである、ブログ、メールではそれぞれ別のウィジェットエリアを個別に指定する事もできます。</p>
<ul>
<li>新しいウィジェットエリアを作成するには、「新規追加」ボタンをクリックします。</li>
<li>既存のウィジェットエリアを編集するには、対象のウィジェットエリアの操作欄にある<?php $bcBaser->img('admin/icn_tool_edit.png') ?>をクリックします。</li>
</ul>
<p><small>※ なお、ウィジェットエリアを作成、編集する際には、サーバーキャッシュが削除され、一時的に公開ページの表示速度が遅くなってしまいますのでご注意ください。</small></p>
