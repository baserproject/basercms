<?php
/* SVN FILE: $Id$ */
/**
 * グロバールメニュー
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
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<ul id="globalMenu" class="clearfix">
<?php if(empty($menuType)) $menuType = '' ?>
    <?php $globalMenus = $baser->getGlobalMenus($menuType) ?>
    <?php if(!empty($globalMenus)): ?>
        <?php foreach($globalMenus as $key => $globalMenu): ?>
            <?php if($globalMenu['GlobalMenu']['status']): ?>
                <?php if($key == 0): ?>
                    <?php $class = ' class="first"' ?>
                <?php elseif($key == count($globalMenus) - 1): ?>
                    <?php $class = ' class="last"' ?>
                <?php else: ?>
                    <?php $class = '' ?>
                <?php endif ?>
                <?php if($this->base == '/index.php' && $globalMenu['GlobalMenu']['link'] == '/'): ?>
                    <li<?php echo $class ?>><?php echo str_replace('/index.php','',$html->link($globalMenu['GlobalMenu']['name'],$globalMenu['GlobalMenu']['link'])) ?></li>
                <?php else: ?>
                    <li<?php echo $class ?>><?php $baser->link($globalMenu['GlobalMenu']['name'],$globalMenu['GlobalMenu']['link']) ?></li>
                <?php endif ?>
            <?php endif ?>
        <?php endforeach ?>
<?php endif ?>
</ul>