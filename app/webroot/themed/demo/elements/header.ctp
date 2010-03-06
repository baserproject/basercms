<?php
/* SVN FILE: $Id$ */
/**
 * ヘッダー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div id="header">

	<div id="headMain">
		<h1><a href="/"><?php echo $baser->siteConfig['name'] ?></a></h1>
	</div>
	
	<?php if($baser->isTop()): ?>
        <?php $baser->img('/img/img_top_main.jpg',array('alt'=>'Let\'s BaserCMS','border'=>'0')) ?>
	<?php endif ?>
	
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php $baser->element('global_menu') ?>
	</div>
	
	<?php if(!$baser->isTop()): ?>
		<!-- navigation -->
		<div id="navigation">
			<?php $baser->element('navi',array('title_for_element'=>$baser->getContentsTitle())); ?>
		</div>
	<?php endif ?>

</div>