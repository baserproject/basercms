<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ヘッダー
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

<div id="header">
	<div id="headMain">
		<h1>
<?php if(isset($baser->siteConfig['name'])): ?>
			<?php $baser->link($baser->siteConfig['name'],'/') ?>
<?php else: ?>
			<?php echo Configure::read('Baser.title') ?>
<?php endif ?>
<?php if(isset($javascript)): ?>
			　<span id="fontChanger">文字サイズ： <a href="#" onclick="setActiveStyleSheet('large'); return false;">大</a>｜ <a href="#" onclick="setActiveStyleSheet('medium'); return false;">中</a>｜ <a href="#" onclick="setActiveStyleSheet('small'); return false;">小</a> </span>
<?php endif; ?>
		</h1>
		<?php $baser->element('search') ?>
	</div>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php $baser->element('global_menu',array(),false,false) ?>
	</div>
</div>
