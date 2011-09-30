<?php
/* SVN FILE: $Id$ */
/**
 * トップページ
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div id="news" class="clearfix">
	<div class="news" style="margin-right:28px;">
		<h2 id="newsHead01">NEWS RELEASE</h2>
		<div class="body">
			<?php $baser->js('/feed/ajax/1') ?>
		</div>
	</div>
	<div class="news">
		<h2 id="newsHead02">BaserCMS NEWS</h2>
		<div class="body">
			<?php $baser->js('/feed/ajax/2') ?>
		</div>
	</div>
</div>
