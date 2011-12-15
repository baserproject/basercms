<?php
/* SVN FILE: $Id$ */
/**
 * 会社案内
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $baser->setTitle('会社案内') ?>
<?php $baser->setDescription('baserCMS inc.の会社案内ページ') ?>

<h2 class="contents-head">会社案内</h2>
<h3 class="contents-head">会社データ</h3>
<div class="section">
	<table class="row-table-01" cellspacing="0" cellpadding="0">
		<tr>
			<th width="150">会社名</th>
			<td>Baser CMS inc.  [デモ]</td>
		</tr>
		<tr>
			<th>設立</th>
			<td>2009年11月</td>
		</tr>
		<tr>
			<th>所在地</th>
			<td>福岡県福岡市博多区博多駅前（ダミー）</td>
		</tr>
		<tr>
			<th>事業内容</th>
			<td>インターネットサービス業（ダミー）<br />
				WEBサイト制作事業（ダミー）<br />
				WEBシステム開発事業（ダミー）</td>
		</tr>
	</table>
</div>
<h3 class="contents-head">アクセスマップ</h3>
<div class="section"> <?php echo $this->renderElement('googlemaps'); ?> </div>
