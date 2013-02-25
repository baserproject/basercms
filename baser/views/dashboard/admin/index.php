<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ダッシュボード
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<div id="AlertMessage" class="message" style="display:none"></div>

<div class="float-left" style="width:33%">
	<div class="panel-box corner10">
		<h2>baserCMSニュース</h2>
		<?php $bcBaser->js('/feed/ajax/1') ?>
		<br />
		<small>baserCMSについて、不具合の発見・改善要望がありましたら<a href="http://forum.basercms.net" target="_blank">ユーザーズフォーラム</a> よりお知らせください。</small>
	</div>
</div>

<div class="float-left" style="width:33%">
	<div class="panel-box corner10">	
		<h2>現在の状況</h2>
		<h3>固定ページ</h3>
		<ul>
			<li>公開中： <?php echo $publishedPages ?> ページ<br />
				非公開： <?php echo $unpublishedPages ?> ページ<br />
				合　計： <?php echo $totalPages ?> ページ<br />
			</li>
		</ul>
	</div>
</div>

<div class="float-left" style="width:33%">
	<div class="panel-box corner10">
		<h2>最近の動き</h2>
		<div id="DblogList">
			<?php $bcBaser->element('dashboard/index_dblog_list') ?>
		</div>
	</div>
</div>
